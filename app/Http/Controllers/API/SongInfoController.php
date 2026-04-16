<?php

namespace App\Http\Controllers\API;

use App\Models\SongInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

        $title = $track['name'];
        $singer = collect($track['artists'])->pluck('name')->join(', ');
        $year = $this->extractReleaseYear($track['album']['release_date'] ?? null);

        return SongInfo::create([
            'title' => $title,
            'singer' => $singer,
            'year' => $year,
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

        $response = Http::withToken($token)
            ->get('https://api.spotify.com/v1/recommendations', [
                'limit' => 1,
                'seed_genres' => $seedGenre,
            ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();
        return $data['tracks'][0] ?? null;
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
