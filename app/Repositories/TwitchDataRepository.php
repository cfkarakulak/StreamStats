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
    /**
     * Get streams data from Twitch
     *
     * @return Collection
     */
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

    /**
     * Store fetched streams in the DB
     *
     * @param $streams array
     * @param $count int
     * @return boolean
     */
    public function storeStreams(array $streams, int $count)
    {
        DB::table('twitch_stream_tag')->delete();
        DB::table('twitch_stream')->delete();

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

    /**
     * Store fetched stream tags in the DB
     *
     * @param $tags array
     * @param $count int
     * @return boolean
     */
    public function storeStreamTags(array $tags, int $count)
    {
        return DB::update(
            sprintf(
                <<<SQL
                    INSERT INTO ss_twitch_stream_tag
                        (
                            stream_id,
                            tag
                        )
                    VALUES %s
                SQL,
                str_repeat('(?,?)' . ',', $count - 1) . '(?,?)',
            ),
            $tags,
        );
    }

    /**
     * Get streams grouped by game ids
     *
     * @return array
     */
    public function getGameStreamsGrouped()
    {
        return DB::select(
            <<<SQL
                SELECT
                    id, game_name,
                    COUNT(*) as number_of_streams
                FROM ss_twitch_stream
                GROUP BY game_id
            SQL
        );
    }

    /**
     * Get game streams and number of viewers
     *
     * @return array
     */
    public function getGameStreamsByViewers()
    {
        return DB::select(
            <<<SQL
                SELECT
                    id, game_name,
                    SUM(number_of_viewers) as number_of_viewers
                FROM ss_twitch_stream
                GROUP BY game_id
                ORDER BY number_of_viewers DESC
            SQL
        );
    }

    /**
     * Get top streams ordered by number of viewers
     *
     * @return array
     */
    public function getTopStreamsByViewers()
    {
        return DB::select(
            <<<SQL
                SELECT
                    id, stream_title,
                    game_name, number_of_viewers
                FROM ss_twitch_stream
                ORDER BY number_of_viewers DESC
                LIMIT 1000
            SQL
        );
    }

    /**
     * Get tags that belong to a stream
     *
     * @return array
     */
    public function getStreamTags()
    {
        return DB::select(
            <<<SQL
                SELECT
                    stream_id, tag
                FROM ss_twitch_stream_tag
            SQL
        );
    }

    /**
     * Group streams to the nearest rounded hour
     *
     * @return array
     */
    public function getStreamsByNearestHours()
    {
        return DB::select(
            <<<SQL
                SELECT
                    id, COUNT(id) as count, started_at,
                    DATE_FORMAT(DATE_ADD(started_at, INTERVAL 30 MINUTE), '%Y-%m-%d %H:00:00') as nearest_hour
                FROM ss_twitch_stream
                GROUP BY nearest_hour
                ORDER BY nearest_hour ASC
            SQL
        );
    }

    /**
     * Get tags' meaningful names
     *
     * @return array
     */
    public function getStreamTagsRespectiveNames(array $tags)
    {
        $params = [
            'first' => 100,
            'tag_id' => $tags,
        ];

        $response = Http::withHeaders([
            'Client-Id' => env('TWITCH_CLIENT_ID'),
            'Authorization' => sprintf('Bearer %s', env('TWITCH_APP_ACCESS_TOKEN')),
        ])->get(
            sprintf('%s/%s?%s', env('TWITCH_API_HELIX'), 'tags/streams', http_build_query($params))
        );

        return $response->json()['data'];
    }
}
