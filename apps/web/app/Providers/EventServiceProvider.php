<?php

namespace App\Providers;

use App\Events\DrawCompleted;
use App\Events\EntryAccepted;
use App\Events\EntryFlagged;
use App\Events\WinnerPublished;
use App\Listeners\QueueWebhookDelivery;
use App\Listeners\QueueWebhookDeliveryForDraw;
use App\Listeners\QueueWebhookDeliveryForFlaggedEntry;
use App\Listeners\QueueWebhookDeliveryForWinner;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        EntryAccepted::class => [
            QueueWebhookDelivery::class,
        ],
        EntryFlagged::class => [
            QueueWebhookDeliveryForFlaggedEntry::class,
        ],
        DrawCompleted::class => [
            QueueWebhookDeliveryForDraw::class,
        ],
        WinnerPublished::class => [
            QueueWebhookDeliveryForWinner::class,
        ],
    ];
}
