<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required', 
                    'string', 
                    'email', 
                    'max:255',
                    'unique:users,email'
                ],
                'homebase' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
                'role' => ['required', 'exists:roles,name'],
            ], [
                'email.unique' => 'Email sudah digunakan oleh user lain.',
                'email.required' => 'Email harus diisi.',
                'email.email' => 'Format email tidak valid.',
                'name.required' => 'Nama harus diisi.',
                'homebase.required' => 'Homebase harus diisi.',
                'password.required' => 'Password harus diisi.',
                'password.min' => 'Password minimal 8 karakter.',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'homebase' => $validated['homebase'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole($validated['role']);

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('show-add-user-modal', true);
        }
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
            'canUpdateRole' => true
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'homebase' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:1024'],
            'signature' => ['nullable', 'image', 'max:1024'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('signature')) {
            if ($user->signature_path) {
                Storage::disk('public')->delete($user->signature_path);
            }
            $user->signature_path = $request->file('signature')->store('signatures', 'public');
        }

        $user->update([
            'name' => $request->name,
            'homebase' => $request->homebase,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function toggleActive(User $user)
    {
        // Prevent self-deactivation
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        // Prevent deactivating last admin
        if ($user->hasRole('admin') && User::role('admin')->where('is_active', true)->count() <= 1) {
            return redirect()->back()->with('error', 'Cannot deactivate the last active admin.');
        }

        $user->is_active = !$user->is_active;
        $user->inactive_reason = $user->is_active ? null : request('reason');
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "User has been {$status} successfully.");
    }
} 