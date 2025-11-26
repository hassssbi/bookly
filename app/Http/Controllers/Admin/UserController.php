<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // List + search + filters + sorting
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        if (! in_array($sort, ['id', 'name', 'email', 'role', 'created_at'], true)) {
            $sort = 'created_at';
        }
        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $users = $query
            ->orderBy($sort, $direction)
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'sort', 'direction'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,seller,customer',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User created successfully');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,seller,customer',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Optional: prevent deleting yourself
        if (auth()->id() === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('status', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User deleted');
    }

    // Quick role change (optional)
    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|in:admin,seller,customer',
        ]);

        $user->role = $data['role'];
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Updated {$user->name}'s role to {$user->role}");
    }
}
