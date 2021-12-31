<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Lisennk\LaravelSlackEvents\Events\Message;

class SlackMessageProcessingService
{
    public function __construct(
        protected SlackMessageParsingService $slackMessageParsingService
    ) {}

    public function processMessage(Message $message): void
    {
        if (!$this->isLegendaryMessage($message)) {
            return;
        }

        $sender = $this->getSender($message);
        $receivers = $this->getReceivers($message);
        $identifierCount = $this->getIdentifierCount($message);

        // TODO: Validate if user has enough balance to send this amount of kudo's

        foreach ($receivers as $receiver) {
            Transaction::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'count' => $identifierCount
            ]);
        }
    }

    protected function isLegendaryMessage(Message $message): bool
    {
        return $this->messageContainsLegendIdentifier($message)
            && $this->peopleAreTaggedInMessage($message);
    }

    protected function peopleAreTaggedInMessage(Message $message): bool
    {
        return $this->slackMessageParsingService->getTaggedSlackUserIds($message)->isNotEmpty();
    }

    protected function messageContainsLegendIdentifier(Message $message): bool
    {
        return $this->getIdentifierCount($message) > 0;
    }

    protected function getSender(Message $message): User
    {
        return $this->getUserBySlackId(
            $this->slackMessageParsingService->getSenderSlackUserId($message)
        );
    }

    protected function getReceivers(Message $message): Collection
    {
        return $this->slackMessageParsingService->getTaggedSlackUserIds($message)->map(function (string $slackUserId) {
            return $this->getUserBySlackId($slackUserId);
        });
    }

    protected function getIdentifierCount(Message $message): int
    {
        return substr_count(
            $this->slackMessageParsingService->getMessageText($message),
            config('legend-bot.identifier')
        );
    }

    protected function getUserBySlackId(string $slackId): User
    {
        $user = User::firstWhere('slack_user_id', $slackId);
        if ($user instanceof User) {
            return $user;
        }

        return User::create([
            'slack_user_id' => $slackId,
            'slack_user_name' => $slackId, // TODO: Get User name from Slack API
        ]);
    }

    protected function getUserBySlackName(string $slackName): User
    {
        $user = User::firstWhere('slack_user_name', $slackName);
        if ($user instanceof User) {
            return $user;
        }

        return User::create([
            'slack_user_id' => $slackName, // TODO: Get User ID from Slack API
            'slack_user_name' => $slackName,
        ]);
    }
}
