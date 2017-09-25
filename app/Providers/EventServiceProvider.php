<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Event::listen("tymon.jwt.*", function($eventName, $data) {
           switch($eventName) {
               case 'tymon.jwt.invalid':
               case 'tymon.jwt.expired':
                   throw $data[0];
                   break;
               default:
                   break;
           }
        });
        //
    }
}
