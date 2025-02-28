<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('homebase', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->back()->with('success', 'User role updated successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'homebase' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'homebase' => $request->homebase,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot remove your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User has been removed successfully.');
    }

    public function resetPassword(User $user)
    {
        $validated = request()->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Reset password to new password
        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password has been reset successfully');
    }

    public function edit(User $user)
    {
        return view('profile.edit', [
            'user' => $user,
            'canUpdateRole' => true // Flag untuk menandai bahwa ini admin yang edit
        ]);
    }
} 