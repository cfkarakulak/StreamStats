<?php

namespace App\Repositories;

use App\Contracts\TwitchContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Http;

class TwitchRepository implements TwitchContract
{
    public function getTopStreams(int $count = 1000): Collection
    {
        $response = Http::twitch()->get('streams?first=10');

        return collect($response->json()['data']);
    }
}
