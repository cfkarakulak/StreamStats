<?php

namespace App\Http\Controllers;

use App\Contracts\TwitchContract;
use App\Repositories\TwitchRepository;

class TwitchStatsController
{
    public function __construct(
        protected TwitchContract $twitchRepository
    ) {
    }

    /**
     * Render Twitch stream stats
     *
     * @return array
     */
    public function render()
    {
        $data = [
            'streams_by_game' => $this->twitchRepository->getGameStreamsGrouped(),
            'games_by_viewers' => $this->twitchRepository->getGameStreamsByViewers(),
            'streams_by_viewers' => $this->twitchRepository->getTopStreamsByViewers(),
            'streams_by_nearest_hours' => $this->twitchRepository->getStreamsByNearestHour(),
        ];

        return response()->json();
    }
}
