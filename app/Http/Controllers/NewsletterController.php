<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The email must be valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;

        // Check if already subscribed
        $existing = Entry::query()
            ->where('collection', 'newsletter_subscribers')
            ->where('email', $email)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You are already subscribed with this email.'
            ], 409);
        }

        // Create new entry
        Entry::make()
            ->collection('newsletter_subscribers')
            ->slug(Str::slug($email)) // required
            ->data([
                'title' => $email,      // required
                'email' => $email,
                'subscribed_at' => now(),
            ])
            ->save();

        return response()->json([
            'message' => 'Thank you for subscribing!'
        ]);
    }

}
