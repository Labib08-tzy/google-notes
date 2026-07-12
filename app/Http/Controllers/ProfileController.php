<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        // Ensure settings exist for the view
        $user->getOrCreateSettings();

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        $user->name = $request->input('name');
        $user->display_name = $request->input('display_name');

        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            // Sanitize and create safe filename
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $image->getClientOriginalName());
            
            // Delete old avatar if it was locally stored
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                @unlink(public_path($user->avatar));
            }

            $image->move(public_path('uploads/avatars'), $filename);
            $user->avatar = '/uploads/avatars/' . $filename;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Delete stored avatar locally if it exists and is local
        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            @unlink(public_path($user->avatar));
        }

        // 2. Sign out the user
        Auth::logout();

        // 3. Delete user record (Cascade will delete settings and notes automatically)
        $user->delete();

        // 4. Invalidate session & regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'Your account has been deleted successfully.');
    }
}
