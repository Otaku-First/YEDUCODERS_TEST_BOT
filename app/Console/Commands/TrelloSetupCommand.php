<?php

namespace App\Console\Commands;

use App\Services\User\TrelloService;
use Illuminate\Console\Command;

class TrelloSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trello:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';



    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trelloService = app()->make(\App\Services\User\TrelloService::class);

        $this->info($trelloService->setupWebhook());

    }
}
