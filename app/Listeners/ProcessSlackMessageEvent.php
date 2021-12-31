<?php

namespace App\Listeners;

use App\Services\SlackMessageProcessingService;
use Lisennk\LaravelSlackEvents\Events\Message;

class ProcessSlackMessageEvent
{
    public function __construct(
        protected SlackMessageProcessingService $slackMessageProcessingService
    ) {}

    public function handle(Message $event)
    {
        $this->slackMessageProcessingService->processMessage($event);
    }
}
