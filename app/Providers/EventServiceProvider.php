<?php

namespace App\Providers;

use App\Events\Ots\Events\ConfirmRequestEvent;
use App\Events\Ots\Events\RequestEvent;
use App\Events\Ots\Messages\NewMessage;
use App\Listeners\Ots\Events\ConfirmRequestListener;
use App\Listeners\Ots\Events\RequestListener;
use App\Listeners\Ots\Messages\NewMessageListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ConfirmRequestEvent::class => [
            ConfirmRequestListener::class
        ],
        RequestEvent::class => [
            RequestListener::class
        ],
        NewMessage::class => [
            NewMessageListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
