<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SlackUserService;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legendbot:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $slackUserService = app(SlackUserService::class);
        
        $user = User::first();
        
        $userData = $slackUserService->getUserInfo($user->slack_user_id);
        var_dump($userData);die;
        
        return Command::SUCCESS;
    }
}
