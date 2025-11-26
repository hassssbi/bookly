<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\Request;

class SellerProfileController extends Controller
{
    // ADMIN: list all sellers (for approval)
    public function index()
    {
        return SellerProfile::with('user')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    // SELLER: create or update own profile (apply / re-apply)
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'seller') {
            return response()->json(['message' => 'Only sellers can have a seller profile'], 403);
        }

        $data = $request->validate([
            'store_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $profile = SellerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'store_name' => $data['store_name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => 'pending',  // every submit goes back to pending
                'rejection_reason' => null,
            ]
        );

        return response()->json($profile, 201);
    }

    // SELLER: show own profile status
    public function showCurrent(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'seller') {
            return response()->json(['message' => 'Only sellers have a seller profile'], 403);
        }

        $profile = $user->sellerProfile; // from User::sellerProfile relationship

        if (! $profile) {
            return response()->json([
                'has_profile' => false,
                'status' => null,
                'profile' => null,
            ]);
        }

        return response()->json([
            'has_profile' => true,
            'status' => $profile->status,           // pending / approved / rejected
            'profile' => $profile,                   // full profile data
        ]);
    }

    // ADMIN: approve / reject seller
    public function update(Request $request, SellerProfile $sellerProfile)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string',
        ]);

        if ($data['status'] === 'approved') {
            $data['rejection_reason'] = null;
        }

        $sellerProfile->update($data);

        return response()->json($sellerProfile->load('user'));
    }
}
