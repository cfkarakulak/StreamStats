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
            sprintf('https://id.twitch.tv/oauth2/token?%s', http_build_query([
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
        ])->get('https://api.twitch.tv/helix/users');

        if ($response->status() == 200) {
            return collect($response->json()['data'][0]);
        }

        throw new Exception('Something went wrong.');
    }

    public function storeUser(array $data)
    {
        return DB::table('twitch_user')->insert($data);
    }

    public function getUsersFollowedStreams(string $token, int $user_id)
    {
        $response = Http::withHeaders([
            'Client-Id' => env('TWITCH_CLIENT_ID'),
            'Authorization' => sprintf('Bearer %s', $token),
        ])->get(sprintf('https://api.twitch.tv/helix/streams/followed?user_id=%s', $user_id));

        if ($response->status() == 200) {
            return collect($response->json()['data'][0]);
        }

        throw new Exception('Something went wrong.');
    }
}
