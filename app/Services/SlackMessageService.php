<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Lisennk\LaravelSlackEvents\Events\Message;
use Illuminate\Support\Collection;

class SlackMessageService
{
    public function processMessage(Message $message): void
    {
        if (!$this->isLegendaryMessage($message)) {
            return;
        }

        $sender = $this->getSender($message);
        $receivers = $this->getReceivers($message);
        $identifierCount = $this->getIdentifierCount($message);

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
        return $this->peopleAreTaggedInMessage($message)
            && $this->messageContainsLegendIdentifier($message);
    }

    protected function peopleAreTaggedInMessage(Message $message): bool
    {
        return str_contains($message->data->text, '@');
    }

    protected function messageContainsLegendIdentifier(Message $message): bool
    {
        return str_contains($message->data->text, config('legend-bot.identifier'));
    }

    protected function getSender(Message $message): User
    {
        return $this->getUserBySlackId($message->data->user);
    }

    protected function getReceivers(Message $message): Collection
    {
        $words = explode(' ', $message->data->text);

        return collect($words)->filter(function(string $word) {
            return str_starts_with($word, '@');
        })->map(function (string $word) {
            return $this->getUserBySlackName($word);
        });
    }

    protected function getIdentifierCount(Message $message): int
    {
        $words = explode(' ', $message->data->text);

        return collect($words)->filter(function(string $word) {
            return $word === config('legend-bot.identifier');
        })->count();
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
