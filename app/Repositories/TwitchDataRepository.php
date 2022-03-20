<?php

namespace App\Repositories;

use App\Contracts\TwitchDataContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Exception;
use Http;
use Log;

class TwitchDataRepository implements TwitchDataContract
{
    public function getStreams(): Collection
    {
        $data = collect();
        $params = [
            'first' => 100,
        ];

        foreach (range(0, 10) as $i) {
            $response = Http::withHeaders([
                'Client-Id' => env('TWITCH_CLIENT_ID'),
                'Authorization' => sprintf('Bearer %s', env('TWITCH_APP_ACCESS_TOKEN')),
            ])->get(
                sprintf('%s/%s?%s', env('TWITCH_API_HELIX'), 'streams', http_build_query($params))
            );

            $params = array_merge($params, [
                'after' => $response['pagination']['cursor'],
            ]);

            $data->push(array_filter($response['data'], function ($stream) {
                return data_get($stream, 'game_id');
            }));

            usleep(1000);
        }

        return $data->collapse()->take(1000)->shuffle();
    }

    public function storeStreams(array $streams, int $count)
    {
        return DB::update(
            sprintf(
                <<<SQL
                    INSERT INTO ss_twitch_stream
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
                    id,
                    game_name,
                    COUNT(*) as number_of_streams
                FROM ss_twitch_stream
                GROUP BY game_id
            SQL
        );
    }

    public function getGameStreamsByViewers()
    {
        return DB::select(
            <<<SQL
                SELECT
                    id,
                    game_name,
                    SUM(number_of_viewers) as number_of_viewers
                FROM ss_twitch_stream
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
                    id,
                    stream_title,
                    number_of_viewers
                FROM ss_twitch_stream
                ORDER BY number_of_viewers DESC
                LIMIT 100
            SQL
        );
    }

    public function getStreamsByNearestHours()
    {
        return DB::select(
            <<<SQL
                SELECT
                    id, COUNT(id) as count,
                    stream_title, started_at,
                    DATE_FORMAT(DATE_ADD(started_at, INTERVAL 30 MINUTE), '%Y-%m-%d %H:00:00') as nearest_hour
                FROM ss_twitch_stream
                GROUP BY nearest_hour
                ORDER BY nearest_hour ASC
            SQL
        );
    }
}
