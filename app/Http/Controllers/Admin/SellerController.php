<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    // List all sellers (pending first)
    /* public function index()
    {
        $sellers = SellerProfile::with('user')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.sellers.index', compact('sellers'));
    } */

    public function index(Request $request)
    {
        $query = SellerProfile::with('user');

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Search by user name/email or store name
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('store_name', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
            });
        }

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        if (! in_array($sort, ['id', 'store_name', 'status', 'created_at'], true)) {
            $sort = 'created_at';
        }
        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        // For status we could do a custom FIELD, but keep it simple by column sort
        $sellers = $query
            ->orderBy($sort, $direction)
            ->paginate(20)
            ->withQueryString();

        return view('admin.sellers.index', compact('sellers', 'sort', 'direction'));
    }

    // View a single seller profile
    public function show(SellerProfile $sellerProfile)
    {
        $sellerProfile->load('user');

        return view('admin.sellers.show', [
            'seller' => $sellerProfile,
        ]);
    }

    // Approve / reject / set pending
    /* public function updateStatus(Request $request, SellerProfile $sellerProfile)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string',
        ]);

        if ($data['status'] === 'approved') {
            $data['rejection_reason'] = null;
        }

        $sellerProfile->update($data);

        return redirect()
            ->route('admin.sellers.index')
            ->with('status', "Seller {$sellerProfile->user->name} updated to {$sellerProfile->status}");
    } */

    public function updateStatus(Request $request, SellerProfile $sellerProfile)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string',
        ]);

        if ($data['status'] === 'approved') {
            $data['rejection_reason'] = null;

            // Make sure user has seller role
            $user = $sellerProfile->user;
            if ($user->role !== 'seller') {
                $user->role = 'seller';
                $user->save();
            }
        }

        // Optional: if you ever want to auto-demote sellers when rejected:
        // elseif ($data['status'] === 'rejected' && $sellerProfile->user->role === 'seller') {
        //     $user = $sellerProfile->user;
        //     $user->role = 'customer';
        //     $user->save();
        // }

        $sellerProfile->update($data);

        return redirect()
            ->route('admin.sellers.index')
            ->with('status', "Seller {$sellerProfile->user->name} updated to {$sellerProfile->status}");
    }
}
