<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\Facades\Collection;
use Illuminate\Support\Str;

class CustomerAddressController extends Controller
{
    public function save(Request $request)
    {
        $customerId = session('customer_id');

        if (! $customerId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validate request
        $validated = $request->validate([
            'address_type'   => 'required|in:billing,shipping',
            'first_name'     => 'required|string',
            'last_name'      => 'required|string',
            'email'          => 'required|email',
            'phone_number'   => 'required|string',
            'address'        => 'required|string',
            'city'           => 'required|string',
            'state'          => 'required|string',
            'pin_code'       => 'required|string',
            'country'        => 'required|string',
        ]);

        // Find existing address entry for this customer & type
        $existing = Entry::query()
            ->where('collection', 'addresses')
            ->where('address_type', $request->address_type)
            ->whereJsonContains('customer', $customerId)
            ->first();

        $entry = $existing ?? Entry::make()
            ->collection('addresses')
            ->slug(Str::uuid());

        $entry
            ->set('customer', $customerId) // store as array
            ->set('address_type', $validated['address_type'])
            ->set('first_name', $validated['first_name'])
            ->set('last_name', $validated['last_name'])
            ->set('email', $validated['email'])
            ->set('phone_number', $validated['phone_number'])
            ->set('address', $validated['address'])
            ->set('city', $validated['city'])
            ->set('state', $validated['state'])
            ->set('pin_code', $validated['pin_code'])
            ->set('country', $validated['country'])
            ->save();

        return response()->json(['message' => 'Address saved successfully.']);
    }

    public function getAddress(Request $request)
    {
        $customerId = session('customer_id');

        if (! $customerId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $billing = Entry::query()
            ->where('collection', 'addresses')
            ->whereJsonContains('customer', $customerId)
            ->where('address_type', 'billing')
            ->first();

        $shipping = Entry::query()
            ->where('collection', 'addresses')
            ->whereJsonContains('customer', $customerId)
            ->where('address_type', 'shipping')
            ->first();

        return response()->json([
            'billing' => $billing ? $billing->data() : null,
            'shipping' => $shipping ? $shipping->data() : null,
        ]);
    }

}
