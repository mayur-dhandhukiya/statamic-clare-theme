<?php

namespace App\Tags;

use Statamic\Tags\Tags;
use Statamic\Facades\Entry;
use Carbon\Carbon;

class CustomerSession extends Tags
{
    protected static $handle = 'customer_session';

    /**
     * {{ customer_session }}
     */
    public function index()
    {
        return [
            'customer_logged_in'   => session()->get('customer_logged_in', false),
            'customer_id'          => session()->get('customer_id'),
            'customer_full_name'   => session()->get('customer_full_name'),
            'customer_email'       => session()->get('customer_email'),
            'customer_profile_img' => $this->getCustomerProfileImage(),
        ];
    }

    /**
     * {{ customer_session:check }}
     */
    public function check()
    {
        return session()->get('customer_logged_in', false);
    }

    /**
     * {{ customer_session:check }}
     */
    public function customerId()
    {
        return session()->get('customer_id');
    }

    /**
     * {{ customer_session:first_name }}
     */
    public function firstName()
    {
        $entry = $this->getCustomerEntry();
        $first = $entry?->get('first_name') ?? session('customer_first_name');

        if (!$first && $entry?->get('full_name')) {
            $parts = explode(' ', $entry->get('full_name'), 2);
            $first = $parts[0] ?? null;
        }

        return $first;
    }

    /**
     * {{ customer_session:last_name }}
     */
    public function lastName()
    {
        $entry = $this->getCustomerEntry();
        $last = $entry?->get('last_name') ?? session('customer_last_name');

        if (!$last && $entry?->get('full_name')) {
            $parts = explode(' ', $entry->get('full_name'), 2);
            $last = $parts[1] ?? null;
        }

        return $last;
    }

    /**
     * {{ customer_session:name }}
     */
    public function name()
    {
        $entry = $this->getCustomerEntry();
        $name = $entry?->get('full_name') ?? session()->get('customer_full_name');

        return $name;
    }

    /**
     * {{ customer_session:email }}
     */
    public function email()
    {
        $entry = $this->getCustomerEntry();
        $email = $entry?->get('email') ?? session()->get('customer_email');
        
        return $email;
    }

    /**
     * {{ customer_session:profile_image }}
     */
    public function profileImage()
    {
        return $this->getCustomerProfileImage() ?? '/assets/images/user-placeholder.png';
    }

    /**
     * {{ customer_session:details }}
     */
    public function details()
    {
        $entry = $this->getCustomerEntry();

        if (!$entry) {
            return [];
        }

        // Retrieve values from session if available
        $firstName = session('customer_first_name');
        $lastName = session('customer_last_name');
        $fullName = session('customer_full_name');

        // If any name parts are missing, try to derive them from entry
        if (!$firstName || !$lastName) {
            $entryFirst = $entry->get('first_name');
            $entryLast = $entry->get('last_name');

            if ($entryFirst && $entryLast) {
                $firstName = $entryFirst;
                $lastName = $entryLast;
            } elseif ($entry->get('full_name')) {
                $parts = explode(' ', $entry->get('full_name'), 2);
                $firstName = $firstName ?: $parts[0];
                $lastName = $lastName ?: ($parts[1] ?? '');
            }
        }

        if (!$fullName && $firstName) {
            $fullName = trim($firstName . ' ' . $lastName);
        }

        $created = $entry->get('created_at');

        return array_merge(
            $entry->data()->toAugmentedArray(),
            [
                'first_name'    => $firstName,
                'last_name'     => $lastName,
                'full_name'     => $fullName,
                'created_date'  => $created ? Carbon::parse($created)->format('M Y') : null,
            ]
        );
    }

    /**
     * Get the profile image URL
     */
    private function getCustomerProfileImage()
    {
        $entry = $this->getCustomerEntry();

        if (!$entry) {
            return null;
        }

        $image = $entry->get('profile');

        if (!$image) {
            return null;
        }

        // Check if it's already a full URL or asset object
        if ($image instanceof \Statamic\Assets\Asset) {
            return $image->url();
        }

        // Fallback: manually prefix if it's a plain string
        return '/assets/' . ltrim($image, '/');
    }

    /**
     * Fetch the customer entry by session ID
     */
    private function getCustomerEntry()
    {
        $id = session('customer_id');

        if (!$id) {
            return null;
        }

        return Entry::find($id);
    }
}
