<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Lisennk\LaravelSlackEvents\Events\Message;

class SlackMessageParsingService
{
    public function getMessageText(Message $message): string
    {
        // Replace "\u00a0" (Unicode non-breaking space) with normal space
        return str_replace( chr( 194 ) . chr( 160 ), ' ', $message->data['text']);
    }

    public function getSenderSlackUserId(Message $message): string
    {
        return $message->data['user'];
    }

    public function getTaggedSlackUserIds(Message $message): Collection
    {
        $words = explode(' ', $this->getMessageText($message));

        return collect($words)->filter(function(string $word) {
            return str_starts_with($word, '<@');
        })->map(function (string $word) {
            $word = ltrim($word, "<@");
            $userId = rtrim($word, ">");

            return $userId;
        })->unique();
    }
}
