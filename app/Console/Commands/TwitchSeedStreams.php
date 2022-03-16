<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\TwitchContract;
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
        protected TwitchContract $twitchRepository
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
        $streams = $this->twitchRepository->getStreams();

        if ($streams->count() <= 0) {
            return self::abort('Twitch API returned with an empty streams response.');
        }

        // sortu kendimiz belirlememiz gerekiyor.
        $data = $streams->map(function ($item) {
            return collect($item)
                ->put('id', Str::uuid()->toString())
                ->only('id', 'title', 'game_id', 'game_name', 'viewer_count', 'started_at')
                ->values()
                ->all();
        });

        $execute = $this->twitchRepository->storeStreams(
            streams: $data->collapse()->toArray(),
            count: $data->count(),
        );

        return 0;
    }

    public static function abort($content)
    {
        Log::info($content);
        return 1;
    }
}
