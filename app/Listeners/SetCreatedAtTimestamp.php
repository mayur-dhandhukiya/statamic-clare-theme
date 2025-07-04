<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Statamic\Events\EntrySaving;
use Carbon\Carbon;

class SetCreatedAtTimestamp
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EntrySaving $event)
    {
        $entry = $event->entry;

        // Only for blogs collection
        if ($entry->collectionHandle() === 'blogs') {

            // Only set if not already set
            if (!$entry->get('created_at')) {
                // $entry->set('created_at', time());
                $entry->set('created_at', Carbon::now());
            }
        }
    }

}
