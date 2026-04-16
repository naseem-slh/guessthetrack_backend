<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SpotifyController extends Controller
{
    public function loginUrl(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $state = Str::random(40);
        Cache::put('spotify_oauth_state:' . $state, $user->id, now()->addMinutes(10));

        $query = http_build_query([
            'client_id' => config('services.spotify.client_id'),
            'response_type' => 'code',
            'redirect_uri' => config('services.spotify.redirect'),
            'scope' => 'user-read-private user-read-email',
            'state' => $state,
            'show_dialog' => true,
        ]);

        return response()->json([
            'authorize_url' => 'https://accounts.spotify.com/authorize?' . $query,
        ]);
    }

    public function callback(Request $request)
    {
        $state = $request->input('state');
        $code = $request->input('code');
        $error = $request->input('error');

        if ($error) {
            return view('spotify_callback', ['message' => 'Spotify login failed: ' . $error]);
        }

        if (!$state || !$code) {
            return view('spotify_callback', ['message' => 'Invalid Spotify callback request.']);
        }

        $userId = Cache::pull('spotify_oauth_state:' . $state);
        if (!$userId) {
            return view('spotify_callback', ['message' => 'Unable to validate Spotify login state.']);
        }

        $user = User::find($userId);
        if (!$user) {
            return view('spotify_callback', ['message' => 'User not found for Spotify connection.']);
        }

        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.spotify.redirect'),
            'client_id' => config('services.spotify.client_id'),
            'client_secret' => config('services.spotify.client_secret'),
        ]);

        if (!$response->successful()) {
            return view('spotify_callback', ['message' => 'Spotify token exchange failed.']);
        }

        $data = $response->json();

        $user->update([
            'spotify_access_token' => $data['access_token'],
            'spotify_refresh_token' => $data['refresh_token'] ?? $user->spotify_refresh_token,
            'spotify_token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
        ]);

        return view('spotify_callback', ['message' => 'Spotify connected successfully! You can return to the dashboard now.']);
    }
}
