<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

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
     * Update Profile Photo
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo_profile' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'photo_profile.max' => 'Ukuran foto maksimal 2MB.',
            'photo_profile.image' => 'File harus berupa gambar.',
        ]);

        $user = Auth::user();

        if ($request->hasFile('photo_profile')) {
            if ($user->photo_profile) {
                Storage::disk('public')->delete($user->photo_profile);
            }
            $path = $request->file('photo_profile')->store('users/profiles', 'public');
            $user->photo_profile = $path;
            $user->save();
        }

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Update Password
     */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'nullable|min:6|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
            return back()->with('success', 'Kredensial keamanan berhasil diperbarui.');
        }

        return back();
    }
}
