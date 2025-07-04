<?php

namespace App\Listeners;

use Statamic\Events\EntrySaving;
use Illuminate\Support\Facades\Hash;

class HashCustomerPassword
{
    /**
     * Handle the event.
     */
    public function handle(EntrySaving $event): void
    {
        $entry = $event->entry;

        // Only hash passwords for the 'products' collection
        if ($entry->collectionHandle() === 'products') {
            $password = $entry->get('password');

            // Only hash if it's not already hashed
            if ($password && !str_starts_with($password, '$2y$')) {
                $entry->set('password', bcrypt($password));
            }
        }
    }
}
