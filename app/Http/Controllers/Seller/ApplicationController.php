<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // If already seller, just send them to seller profile
        if ($user->role === 'seller') {
            return redirect()
                ->route('seller.profile.show')
                ->with('status', 'You are already a seller.');
        }

        $profile = $user->sellerProfile; // may be null or existing (pending/rejected)

        return view('seller.apply', compact('user', 'profile'));
    }

    public function submit(Request $request)
    {
        $user = $request->user();

        // If already seller, no need to apply
        if ($user->role === 'seller') {
            return redirect()
                ->route('seller.profile.show')
                ->with('status', 'You are already a seller.');
        }

        $data = $request->validate([
            'store_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
        ]);

        $profile = $user->sellerProfile;

        if (! $profile) {
            $profile = new SellerProfile;
            $profile->user_id = $user->id;
        }

        $profile->store_name = $data['store_name'];
        $profile->phone = $data['phone'] ?? null;
        $profile->address = $data['address'] ?? null;

        // Every submission (first or re-apply) becomes pending review
        $profile->status = 'pending';
        $profile->rejection_reason = null;

        $profile->save();

        return redirect()
            ->route('seller.apply.show')
            ->with('status', 'Your application has been submitted. An admin will review it soon.');
    }
}
