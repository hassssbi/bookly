<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $profile = $user->sellerProfile; // hasOne relation on User

        return view('seller.profile.show', compact('user', 'profile'));
    }

    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = $user->sellerProfile;

        return view('seller.profile.edit', compact('user', 'profile'));
    }

    public function storeOrUpdate(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'store_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
        ]);

        /** @var SellerProfile|null $profile */
        $profile = $user->sellerProfile;

        // First-time profile: set status to pending
        if (! $profile) {
            $profile = new SellerProfile;
            $profile->user_id = $user->id;
            $profile->status = 'pending';
        }

        $profile->store_name = $data['store_name'];
        $profile->phone = $data['phone'] ?? null;
        $profile->address = $data['address'] ?? null;

        // Keep existing status + rejection_reason; seller cannot change those
        $profile->save();

        return redirect()
            ->route('seller.profile.show')
            ->with('status', 'Your seller profile has been saved.');
    }
}
