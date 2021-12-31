<?php

namespace App\Services;

use JoliCode\Slack\Api\Model\ObjsUser;
use JoliCode\Slack\Client;
use JoliCode\Slack\ClientFactory;

class SlackUserService
{
    protected Client $slackClient;

    public function __construct()
    {
        $this->slackClient = ClientFactory::create(config('slack.token'));
    }

    public function findUser(string $userId): ?ObjsUser
    {
        return $this->slackClient->usersInfo(['user' => $userId])->getUser();
    }

    public function getAllUsers(): array
    {
        return $this->slackClient->usersList()->getMembers() ?? [];
    }
}
