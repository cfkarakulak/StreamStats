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

    public function getGameStreamsGrouped()
    {
        return DB::select(
            <<<SQL
                SELECT
                    game_name,
                    COUNT(*) as number_of_streams
                FROM ss_twitch_streams
                GROUP BY game_id
            SQL
        );
    }

    public function getGameStreamsByViewers()
    {
        return DB::select(
            <<<SQL
                SELECT
                    game_name,
                    SUM(number_of_viewers) as number_of_viewers
                FROM ss_twitch_streams
                GROUP BY game_id
                ORDER BY number_of_viewers DESC
            SQL
        );
    }

    public function getTopStreamsByViewers()
    {
        return DB::select(
            <<<SQL
                SELECT
                    stream_title,
                    number_of_viewers
                FROM ss_twitch_streams
                ORDER BY number_of_viewers DESC
                LIMIT 100
            SQL
        );
    }

    public function getStreamsByNearestHour()
    {
        return DB::select(
            <<<SQL
                SELECT
                    COUNT(id) as count,
                    stream_title, started_at,
                    DATE_FORMAT(DATE_ADD(started_at, INTERVAL 30 MINUTE), '%Y-%m-%d %H:00:00') as nearest_hour
                FROM ss_twitch_streams
                GROUP BY nearest_hour
                ORDER BY count DESC
            SQL
        );
    }
}
