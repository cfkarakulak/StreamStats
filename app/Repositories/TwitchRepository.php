<?php

namespace App\Repositories;

use App\Contracts\TwitchContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Http;
use Log;

class TwitchRepository implements TwitchContract
{
    public function getStreams(): Collection
    {
        $data = collect();
        $params = [
            'first' => 100,
        ];

        foreach (range(0, 10) as $i) {
            $response = Http::twitch()->get(
                sprintf('%s?%s', 'streams', http_build_query($params))
            )->json();

            $params = array_merge($params, [
                'after' => $response['pagination']['cursor'],
            ]);

            $data->push(array_filter($response['data'], function ($stream) {
                return data_get($stream, 'game_id');
            }));

            usleep(1000);
        }

        return $data->collapse()->take(1000);
    }

    public function storeStreams(array $streams, int $count)
    {
        return DB::update(
            sprintf(
                <<<SQL
                    INSERT INTO ss_twitch_streams
                        (
                            id,
                            game_id,
                            game_name,
                            stream_title,
                            number_of_viewers,
                            started_at
                        )
                    VALUES %s
                SQL,
                str_repeat('(?,?,?,?,?,?)' . ',', $count - 1) . '(?,?,?,?,?,?)',
            ),
            $streams,
        );
    }
}
