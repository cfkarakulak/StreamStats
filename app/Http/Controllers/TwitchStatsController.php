<?php

namespace App\Http\Controllers;

use App\Contracts\TwitchUserContract;
use App\Contracts\TwitchDataContract;
use App\Repositories\TwitchUserRepository;
use App\Repositories\TwitchDataRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
    public function index(Request $request)
    {
        $user = collect($request->session()->get('user'));

        if ($user->has('id')) {
            $data = [
                'streams_by_game' => $this->twitchDataRepository->getGameStreamsGrouped(),
                'games_by_viewers' => $this->twitchDataRepository->getGameStreamsByViewers(),
                'streams_by_nearest_hour' => $this->twitchDataRepository->getStreamsByNearestHour(),
                'streams_by_viewers' => $streams = collect(
                    $this->twitchDataRepository->getTopStreamsByViewers()
                )->keyBy('id'),
                'streams_followed_by_user' => $streams->intersectByKeys(
                    $following = $this->twitchUserRepository->getUsersFollowedStreams(
                        token: $user->get('access_token'),
                        user_id: $user->get('id'),
                    )->keyBy('id')
                ),
                'gain_count' => $streams->min('number_of_viewers') - $following->min('viewer_count'),
            ];
        }

        return Inertia::render('Index', [
            'data' => $data ?? [],
            'authenticated' => (bool) $user->has('id'),
            'twitch_oauth_uri' => $user->has('id') ? url()->to('/logout') : sprintf(
                '%s/authorize?%s',
                env('TWITCH_API_ID'),
                http_build_query([
                    'client_id' => env('TWITCH_CLIENT_ID'),
                    'redirect_uri' => env('TWITCH_REDIRECT_URI'),
                    'response_type' => 'code',
                    'scope' => 'user:read:email user:read:follows',
                ])
            ),
        ]);
    }

    /**
     * Twitch auth
     *
     * @return array
     */
    public function login(Request $request)
    {
        $auth = $this->twitchUserRepository->getAccessToken($_GET['code'])->only([
            'access_token', 'expires_in', 'refresh_token',
        ]);

        $user = $this->twitchUserRepository->getUser($auth['access_token'])->only([
            'id', 'login', 'email',
        ]);

        $request->session()->put('user', $this->twitchUserRepository->saveUser(
            data: $auth->merge($user)->all(),
        ));

        return redirect()->to('/');
    }

    /**
     * Twitch auth
     *
     * @return array
     */
    public function logout(Request $request)
    {
        $request->session()->forget('user');
        return redirect()->to('/');
    }
}
