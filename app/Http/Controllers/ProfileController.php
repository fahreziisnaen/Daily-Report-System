<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'homebase' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:1024'], // max 1MB
            'signature' => ['nullable', 'image', 'max:1024'],
        ];

        // Tambahkan validasi email hanya jika admin yang mengedit
        if ($request->routeIs('admin.users.edit') && auth()->user()->hasRole('admin')) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id];
        }

        $request->validate($rules);

        if ($request->hasFile('avatar')) {
            if ($request->user()->avatar_path) {
                Storage::disk('public')->delete($request->user()->avatar_path);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $request->user()->avatar_path = $avatarPath;
        }

        if ($request->hasFile('signature')) {
            if ($request->user()->signature_path) {
                Storage::disk('public')->delete($request->user()->signature_path);
            }
            $signaturePath = $request->file('signature')->store('signatures', 'public');
            $request->user()->signature_path = $signaturePath;
        }

        $data = [
            'name' => $request->name,
            'homebase' => $request->homebase,
        ];

        // Tambahkan email ke data update hanya jika admin yang mengedit
        if ($request->routeIs('admin.users.edit') && auth()->user()->hasRole('admin')) {
            $data['email'] = $request->email;
        }

        $request->user()->fill($data);
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
