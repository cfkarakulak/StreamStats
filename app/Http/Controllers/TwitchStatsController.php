<?php

namespace App\Http\Controllers;

use App\Contracts\TwitchUserContract;
use App\Contracts\TwitchDataContract;
use App\Repositories\TwitchUserRepository;
use App\Repositories\TwitchDataRepository;
use Str;

class TwitchStatsController
{
    public function __construct(
        protected TwitchUserContract $twitchUserRepository,
        protected TwitchDataContract $twitchDataRepository,
    ) {
    }

    /**
     * Render Twitch stream stats
     *
     * @return array
     */
    public function show()
    {
        $data = [
            'streams_by_game' => $this->twitchDataRepository->getGameStreamsGrouped(),
            'games_by_viewers' => $this->twitchDataRepository->getGameStreamsByViewers(),
            'streams_by_viewers' => $this->twitchDataRepository->getTopStreamsByViewers(),
            'streams_by_nearest_hours' => $this->twitchDataRepository->getStreamsByNearestHour(),
        ];

        return view('index', [
            'twitch_oauth_uri' => sprintf(
                'https://id.twitch.tv/oauth2/authorize?%s',
                http_build_query([
                    'client_id' => env('TWITCH_CLIENT_ID'),
                    'redirect_uri' => env('TWITCH_REDIRECT_URI'),
                    'response_type' => 'code',
                    'scope' => 'user:read:email user:read:follows',
                ])
            )
        ]);
    }

    /**
     * Twitch auth
     *
     * @return array
     */
    public function auth()
    {
        $auth = $this->twitchUserRepository->getAccessToken($_GET['code'])->only([
            'access_token', 'expires_in', 'refresh_token',
        ]);

        $user = $this->twitchUserRepository->getUser($auth['access_token'])->only([
            'id', 'login', 'email',
        ]);

        $this->twitchUserRepository->storeUser(
            data: $auth->merge($user)->all(),
        );

        // $streams = $this->twitchUserRepository->getUsersFollowedStreams(
        //     token: $auth->get('access_token'),
        //     user_id: $user->get('id'),
        // );

        dd($streams);
    }
}
