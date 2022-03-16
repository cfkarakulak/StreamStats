<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\TwitchContract;
use Log;

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
        $streams = $this->twitchRepository->getTopStreams(
            count: 1000,
        );

        return 0;
    }
}
