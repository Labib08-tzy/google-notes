<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the settings form.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $settings = $user->getOrCreateSettings();

        return view('profile.settings', compact('settings'));
    }

    /**
     * Update the user's settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $settings = $user->getOrCreateSettings();

        $validated = $request->validate([
            'theme' => 'required|string|in:light,dark,system',
            'ai_language' => 'required|string|in:auto,id,en',
            'ai_tone' => 'required|string|in:professional,friendly,casual,academic,rudy',
            'ai_response_length' => 'required|string|in:short,medium,long',
            'dashboard_layout' => 'required|string|in:grid,list',
            'notes_per_page' => 'required|integer|min:1|max:100',
            'default_sort' => 'required|string|in:latest,oldest,title_asc,title_desc',
        ]);

        $settings->update($validated);

        return redirect()->route('settings.edit')
            ->with('success', 'Settings updated successfully.');
    }
}
