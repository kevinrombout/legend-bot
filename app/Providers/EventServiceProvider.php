<?php

namespace App\Providers;

use App\Listeners\ProcessSlackMessageEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Lisennk\LaravelSlackEvents\Events\Message as SlackMessageEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SlackMessageEvent::class => [
            ProcessSlackMessageEvent::class,
        ],
    ];
}
