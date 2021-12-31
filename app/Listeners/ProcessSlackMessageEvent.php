<?php

namespace App\Listeners;

use App\Services\SlackMessageService;
use Lisennk\LaravelSlackEvents\Events\Message;

class ProcessSlackMessageEvent
{
    public function __construct(
        protected SlackMessageService $slackMessageService
    ) {}

    public function handle(Message $event)
    {
        $this->slackMessageService->processMessage($event);
    }
}
