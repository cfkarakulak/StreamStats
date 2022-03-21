<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\TwitchDataContract;
use Log;
use Str;

class TwitchSeedStreams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitch:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store top 1000 live streams from the Twitch API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        protected TwitchDataContract $twitchDataRepository
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = $this->twitchDataRepository->getStreams();

        if ($data->count() <= 0) {
            return self::abort('Twitch API returned with an empty streams response.');
        }

        $streams = $data->map(function ($item) {
            return collect($item)
                ->only('id', 'title', 'game_id', 'game_name', 'viewer_count', 'started_at')
                ->values()
                ->all();
        });

        $tags = $data->map(function ($item) {
            return collect($item['tag_ids'])->map(function ($v) use ($item) {
                return [$item['id'], $v];
            });
        })->collapse();

        $execute = $this->twitchDataRepository->storeStreams(
            streams: $streams->collapse()->toArray(),
            count: $streams->count(),
        );

        if ($execute) {
            $this->twitchDataRepository->storeStreamTags(
                tags: $tags->collapse()->toArray(),
                count: $tags->count(),
            );
        }

        return (int) !$execute;
    }

    public static function abort($content)
    {
        Log::info($content);
        return 1;
    }
}
