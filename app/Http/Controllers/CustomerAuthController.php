<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Statamic\Facades\Entry;
use Illuminate\Support\Str;
use Statamic\Facades\Entry as EntryAPI;
use Statamic\Facades\Collection;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Asset;
use Illuminate\Support\Facades\Cache;
use App\Helpers\GlobleHelper;

class CustomerAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'agree' => 'accepted',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $existing = Entry::query()
            ->where('collection', 'customers')
            ->where('email', $request->email)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Email already registered'], 409);
        }

        $full_name = $request->full_name;

        $first_name = '';
        if ($full_name) {
            $parts = explode(' ', $full_name, 2);
            $first_name = $parts[0] ?? null;
        }

        $last_name = '';
        if ($full_name) {
            $parts = explode(' ', $full_name, 2);
            $last_name = $parts[1] ?? null;
        }

        $entry = Entry::make()
            ->collection('customers')
            ->slug(Str::uuid())
            ->data([
                'title' => $full_name,
                'full_name' => $full_name,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'agree' => true,
                'created_at' => now()->format('Y-m-d H:i:s'),
            ]);

        $entry->save();

        return response()->json(['message' => 'Registered successfully']);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $entry = Entry::query()
            ->where('collection', 'customers')
            ->where('email', $request->email)
            ->first();

        if (!$entry || !Hash::check($request->password, $entry->get('password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        Session::put('customer_logged_in', true);
        Session::put('customer_id', $entry->id());
        Session::put('customer_email', $entry->get('email'));
        Session::put('customer_full_name', $entry->get('full_name'));

        // After successful login
        $customerId = session('customer_id');

        $userCart = EntryAPI::query()
            ->where('collection', 'carts')
            ->where('customer', $customerId)
            ->first();

        if (session()->has('cart_id')) {
            $guestCart = Entry::find(session('cart_id'));

            if ($guestCart) {
                if ($userCart) {
                    // Merge items
                    $guestItems = $guestCart->get('cart_items') ?? [];
                    $userItems = $userCart->get('cart_items') ?? [];

                    foreach ($guestItems as $guestItem) {
                        $found = false;

                        foreach ($userItems as &$userItem) {
                            if ($userItem['product'] === $guestItem['product']) {
                                // Merge quantity and total
                                $userItem['qty'] += $guestItem['qty'];
                                $userItem['total'] = $userItem['qty'] * $userItem['price'];
                                $found = true;
                                break;
                            }
                        }

                        if (! $found) {
                            $userItems[] = $guestItem;
                        }
                    }

                    $userCart->set('cart_items', $userItems);
                    $userCart->set('cart_total', array_sum(array_column($userItems, 'total')));
                    $userCart->save();

                    session()->put('cart_id', $userCart->id());

                    // Delete guest cart
                    $guestCart->delete();
                } else {
                    // No user cart exists, assign guest cart
                    $guestCart->set('customer', $customerId)->save();

                    session()->put('cart_id', $guestCart->id());
                }

                // Clear guest cart session
                // session()->forget('cart_id');
            } else {
                if ($userCart) {
                    session()->put('cart_id', $userCart->id());
                }
            }
        } else {
            if ($userCart) {
                session()->put('cart_id', $userCart->id());
            }
        }

        return response()->json(['message' => 'Login successful']);
    }

    public function logout()
    {
        // Session::forget('customer_id');
        Session::forget(['customer_logged_in', 'customer_id', 'customer_email', 'customer_full_name', 'cart_id']);
        return redirect('/sign-in');
        // return response()->json(['message' => 'Logged out']);
    }

    public function updateAccount(Request $request)
    {
        $customerId = session('customer_id');

        if (! $customerId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $customer = Entry::find($customerId);

        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'profile' => 'nullable|image|max:2048',
            'remove_profile_image' => 'nullable|in:0,1',
        ];

        if ($request->filled('current_password') || $request->filled('new_password')) {
            $rules['current_password'] = 'required';
            $rules['new_password'] = 'required|min:8|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        // Unique email check (excluding current user)
        $duplicate = Entry::query()
            ->where('collection', 'customers')
            ->where('email', $request->email)
            ->where('id', '!=', $customer->id())
            ->first();

        if ($duplicate) {
            $validator->after(function ($validator) {
                $validator->errors()->add('email', 'The email is already taken by another account.');
            });
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 🔐 Password check
        if ($request->filled('current_password')) {
            if (! Hash::check($request->current_password, $customer->get('password'))) {
                return response()->json([
                    'errors' => ['current_password' => ['Current password is incorrect.']]
                ], 422);
            }

            // 🛑 Prevent setting the same password again
            if (Hash::check($request->new_password, $customer->get('password'))) {
                return response()->json([
                    'errors' => ['new_password' => ['New password cannot be the same as the current password.']]
                ], 422);
            }

            $customer->set('password', bcrypt($request->new_password));
        }

        // 🖼️ Handle profile image
        if ($request->boolean('remove_profile_image') && $customer->get('profile')) {
            $oldAsset = Asset::find($customer->get('profile'));
            if ($oldAsset) {
                $oldAsset->delete();
            }
            $customer->set('profile', null);
        }

        if ($request->hasFile('profile')) {
            $file = $request->file('profile');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $container = AssetContainer::find('assets'); // Replace 'assets' if your container handle is different

            // Upload file using Statamic's asset pipeline
            $asset = $container->makeAsset($filename)->upload($file);

            // Assign the path to the customer entry
            $customer->set('profile', $asset->path());
        }

        // 📝 Update customer data
        $customer->set('first_name', $request->first_name);
        $customer->set('last_name', $request->last_name);
        $customer->set('full_name', $request->full_name);
        $customer->set('email', $request->email);
        $customer->save();

        // 🧠 Update session
        session()->put([
            'customer_first_name' => $request->first_name,
            'customer_last_name' => $request->last_name,
            'customer_full_name' => $request->full_name,
            'customer_email' => $request->email,
        ]);

        return response()->json(['message' => 'Account updated successfully.']);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $customer = Entry::query()
            ->where('collection', 'customers')
            ->where('email', $request->email)
            ->first();

        if (! $customer) {
            return response()->json(['message' => 'Email not found.'], 404);
        }

        $token = Str::random(64);
        Cache::put("reset_token_{$token}", $customer->id(), now()->addMinutes(5));

        $resetUrl = url("/reset-password?token={$token}");

        $mailData = [
            'to' => $request->email,
            'subject' => "Reset Your Password",
            'body' => "Click the link to set a new password. For your security, this link will expire in 5 minutes : {$resetUrl}",
            'data' => [],
        ];

        $mailFlag = GlobleHelper::sendMail($mailData);

        $resMessage = 'A reset link has been sent to your email. Please note that it will expire in 5 minutes.';
        if ($mailFlag < 1) {
            $resMessage = 'Something went wrong. Please try again.';
            Cache::forget("reset_token_{$token}");
        }

        return response()->json(['message' => $resMessage]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $customerId = Cache::get("reset_token_{$request->token}");

        if (! $customerId) {
            return response()->json(['message' => 'Invalid or expired token.'], 422);
        }

        $customer = Entry::find($customerId);

        if (! $customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        $customer->set('password', bcrypt($request->password))->save();

        Cache::forget("reset_token_{$request->token}");

        return response()->json(['message' => 'Password reset successfully.']);
    }

}
