<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Statamic\Events\EntrySaving;
use App\Listeners\HashCustomerPassword;
use App\Listeners\SetCreatedAtTimestamp;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        EntrySaving::class => [
            HashCustomerPassword::class,
            SetCreatedAtTimestamp::class,
        ],
    ];
}
