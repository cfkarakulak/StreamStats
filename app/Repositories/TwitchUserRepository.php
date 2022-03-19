<?php

namespace App\Repositories;

use App\Contracts\TwitchUserContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Exception;
use Http;
use Log;

class TwitchUserRepository implements TwitchUserContract
{
    public function getAccessToken(string $code)
    {
        $response = Http::post(
            sprintf('%s/token?%s', env('TWITCH_API_ID'), http_build_query([
                'client_id' => env('TWITCH_CLIENT_ID'),
                'client_secret' => env('TWITCH_CLIENT_SECRET'),
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => env('TWITCH_REDIRECT_URI'),
            ]))
        );

        if ($response->status() == 200) {
            return collect($response->json());
        }

        throw new Exception('Something went wrong.');
    }

    public function getUser(string $token)
    {
        $response = Http::withHeaders([
            'Client-Id' => env('TWITCH_CLIENT_ID'),
            'Authorization' => sprintf('Bearer %s', $token),
        ])->get(
            sprintf('%s/%s', env('TWITCH_API_HELIX'), 'users')
        );

        if ($response->status() == 200) {
            return collect($response->json()['data'][0]);
        }

        throw new Exception('Something went wrong.');
    }

    public function saveUser(array $data)
    {
        if (DB::table('twitch_user')->upsert($data, ['id'], [
            'access_token', 'refresh_token', 'expires_in', 'email', 'login'
        ])) {
            return $data;
        }

        throw new Exception('Error storing user in the database.');
    }

    public function getUsersFollowedStreams(string $token, int $user_id)
    {
        $response = Http::withHeaders([
            'Client-Id' => env('TWITCH_CLIENT_ID'),
            'Authorization' => sprintf('Bearer %s', $token),
        ])->get(
            sprintf('%s/%s?user_id=%s', env('TWITCH_API_HELIX'), 'streams/followed', $user_id)
        );

        if ($response->status() == 200) {
            $data = collect($response->json()['data']);

            if ($data->count() > 0) {
                return $data;
            }

            throw new Exception('No live stream found at the moment that this user is following.');
        }

        throw new Exception('Something went wrong.');
    }
}
