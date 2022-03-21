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
            $streams = collect(
                $this->twitchDataRepository->getTopStreamsByViewers()
            )->keyBy('id');

            $following = $this->twitchUserRepository->getUsersFollowedStreams(
                token: $user->get('access_token'),
                user_id: $user->get('id'),
            )->keyBy('id');

            $data = [
                'streams_by_games' => $this->twitchDataRepository->getGameStreamsGrouped(),
                'games_by_viewers' => $this->twitchDataRepository->getGameStreamsByViewers(),
                'streams_by_nearest_hours' => $this->twitchDataRepository->getStreamsByNearestHours(),
                'streams_by_viewers' => $streams->take(100),
                'streams_followed_by_user' => $streams->intersectByKeys($following),
                'minimum_viewer_count_to_gain' => $streams->min('number_of_viewers') - $following->min('viewer_count'),
                'median_of_streams' => $streams->median('number_of_viewers'),
                'shared_tags' => $this->twitchDataRepository->getStreamTagsRespectiveNames(
                    tags: collect(
                        $this->twitchDataRepository->getStreamTags()
                    )->pluck('tag')->unique()->intersect(
                        $following->pluck('tag_ids')->collapse()->unique()
                    )->values()->all(),
                ),
            ];
        }

        return Inertia::render('Index', [
            'authenticated' => (bool) $user->has('id'),
            'data' => $data ?? [],
            'user' => $user,
            'auth_uri' => $user->has('id') ? url()->to('/logout') : sprintf(
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
     * Login with a Twitch account
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
     * Logout
     *
     * @return array
     */
    public function logout(Request $request)
    {
        $request->session()->forget('user');
        return redirect()->to('/');
    }
}
