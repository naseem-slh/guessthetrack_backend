<?php

namespace App\Http\Controllers\API;

use App\Models\SongInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
class SongInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SongInfo::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->boolean('spotify')) {
            return $this->storeFromSpotify($request);
        }

        $request->validate([
            'title' => 'required|string',
            'singer' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        return SongInfo::create($request->all());
    }

    protected function storeFromSpotify(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->spotify_access_token) {
            return response()->json([
                'success' => false,
                'message' => 'Spotify connection required to create a random song.',
                'data' => ['spotify_login_url' => route('spotify.login-url')],
            ], 403);
        }

        $token = $this->ensureSpotifyAccessToken($user);
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to refresh Spotify credentials. Please reconnect Spotify.',
                'data' => ['spotify_login_url' => route('spotify.login-url')],
            ], 403);
        }

        $track = $this->fetchRandomSpotifyTrack($token);

        if (!$track) {
            return response()->json([
                'success' => false,
                'message' => 'Could not retrieve a random Spotify track at this time.',
            ], 500);
        }

        $title = $track['name']??"Unknown Title";
        $singer = collect($track['artists'])->pluck('name')->join(', ')??"Unknown Artist";
        $year = $this->extractReleaseYear($track['album']['release_date'] ?? null);
        $uri = $track['uri']??":"; // "spotify:track:7lDGg8CFySbkKUrjgzcLlY"

        $trackId = Str::afterLast($uri, ':');
        return SongInfo::create([
            'title' => $title,
            'singer' => $singer,
            'year' => $year,
            'spotify_track_id' => $trackId,
            'spotify_external_urls' => $track['external_urls'],
            'spotify_preview_url' => "",
            'spotify_images' => $track['album']['images'] ?? [],
            'spotify_duration_ms' => $track['duration_ms'],
            'spotify_uri' => $track['uri'],
        ]);
    }

    protected function ensureSpotifyAccessToken(User $user)
    {
        if ($user->spotify_token_expires_at && now()->greaterThan($user->spotify_token_expires_at)) {
            return $this->refreshSpotifyAccessToken($user);
        }

        return $user->spotify_access_token;
    }

    protected function refreshSpotifyAccessToken(User $user)
    {
        if (!$user->spotify_refresh_token) {
            return null;
        }

        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->spotify_refresh_token,
            'client_id' => config('services.spotify.client_id'),
            'client_secret' => config('services.spotify.client_secret'),
        ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();
        $user->update([
            'spotify_access_token' => $data['access_token'],
            'spotify_token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
            'spotify_refresh_token' => $data['refresh_token'] ?? $user->spotify_refresh_token,
        ]);

        return $user->spotify_access_token;
    }

    protected function fetchRandomSpotifyTrack(string $token)
    {
        $genres = ['pop', 'rock', 'hip-hop', 'dance', 'chill', 'jazz', 'rnb', 'indie'];
        $seedGenre = $genres[array_rand($genres)];
        
        logger()->info('Spotify token check', [
            'token_length' => strlen($token),
            'token_start' => substr($token, 0, 20),
        ]);
        
        $response = Http::withToken($token)
    ->get('https://api.spotify.com/v1/search', [
        'q' => 'genre:"' . $seedGenre . '"', // Filter by genre
        'type' => 'track',
        'limit' => 1,               // We only want one song
        'offset' => rand(0, 1000)   ]); // Randomize the offset to get different songs]);

        logger()->info('Spotify API Response', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'body' => $response->body(),
            'headers' => $response->headers(),
        ]);

        if (!$response->successful()) {
            logger()->error('Spotify API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json(),
            ]);
            return null;
        }

        $data = $response->json();
        logger()->info('Spotify tracks received', ['count' => count($data['tracks']['items'] ?? [])]);
                logger()->info('Spotify ', $data['tracks'] );

        logger()->info('Random spotify track', ['track' => $data['tracks']['items'][0] ?? null]);

        return $data['tracks']['items'][0] ?? null;
    }

    protected function extractReleaseYear(?string $releaseDate): int
    {
        if (!$releaseDate) {
            return now()->year;
        }

        return intval(substr($releaseDate, 0, 4));
    }

    /**
     * Display the specified resource.
     */
    public function show(SongInfo $songInfo)
    {
        return $songInfo;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SongInfo $songInfo)
    {
        $request->validate([
            'title' => 'sometimes|string',
            'singer' => 'sometimes|string',
            'year' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        $songInfo->update($request->all());
        return $songInfo;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SongInfo $songInfo)
    {
        $songInfo->delete();
        return response()->noContent();
    }
}
