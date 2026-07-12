<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        $userId = $user->id;

        $stats = Cache::remember("user_{$userId}_stats", now()->addDay(), function () use ($user) {
            return [
                'totalNotes' => $user->notes()->where('is_archived', false)->count(),
                'archivedNotes' => $user->notes()->where('is_archived', true)->count(),
                'favoriteNotes' => $user->notes()->where('is_archived', false)->where('is_favorite', true)->count(),
                'trashNotes' => $user->notes()->onlyTrashed()->count(),
                'pinnedNotes' => $user->notes()->where('is_archived', false)->where('is_pinned', true)->count(),
            ];
        });

        // Real latest 5 active notes (pinned notes first, then latest)
        $latestNotes = $user->notes()
            ->where('is_archived', false)
            ->orderBy('is_pinned', 'desc')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', array_merge($stats, [
            'latestNotes' => $latestNotes
        ]));
    }
}
 