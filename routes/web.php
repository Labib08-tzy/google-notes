<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// Landing Page — redirect authenticated users to dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// Dashboard — auth required
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Notes Custom Actions
    Route::post('/notes/bulk', [NotesController::class, 'bulkAction'])->name('notes.bulk');
    Route::patch('/notes/{note}/favorite', [NotesController::class, 'toggleFavorite'])->name('notes.favorite');
    Route::patch('/notes/{note}/pin', [NotesController::class, 'togglePin'])->name('notes.pin');
    Route::patch('/notes/{note}/archive', [NotesController::class, 'toggleArchive'])->name('notes.archive');
    Route::patch('/notes/{note}/restore', [NotesController::class, 'restore'])->name('notes.restore');
    Route::delete('/notes/{note}/force', [NotesController::class, 'forceDelete'])->name('notes.force-delete');
    Route::get('/archive', [NotesController::class, 'archiveList'])->name('notes.archive-list');
    Route::get('/trash', [NotesController::class, 'trashList'])->name('notes.trash-list');
    // Archive PIN
    Route::post('/notes/{note}/archive-pin', [NotesController::class, 'setArchivePin'])->name('notes.archive-pin.set');
    Route::post('/notes/{note}/archive-pin/verify', [NotesController::class, 'verifyArchivePin'])->name('notes.archive-pin.verify');
    Route::delete('/notes/{note}/archive-pin', [NotesController::class, 'removeArchivePin'])->name('notes.archive-pin.remove');

    // Notes CRUD
    Route::resource('notes', NotesController::class);

    // Tags CRUD
    Route::resource('tags', TagController::class);

    // Notes Custom Actions
    Route::patch('/notes/{note}/autosave', [NotesController::class, 'autosave'])->name('notes.autosave');

    // AI Features
    Route::middleware('throttle:ai_requests')->group(function () {
        Route::post('/ai/summarize', [AiController::class, 'summarize'])->name('ai.summarize');
        Route::post('/ai/improve', [AiController::class, 'improve'])->name('ai.improve');
        Route::post('/ai/continue', [AiController::class, 'continue'])->name('ai.continue');
        Route::post('/ai/title', [AiController::class, 'title'])->name('ai.title');
        Route::post('/ai/explain', [AiController::class, 'explain'])->name('ai.explain');
        Route::post('/ai/translate', [AiController::class, 'translate'])->name('ai.translate');
        Route::post('/ai/chat', [AiController::class, 'chat'])->name('ai.chat');
    });

    // Notes Export
    Route::get('/notes/{note}/export/{format}', [NotesController::class, 'export'])->name('notes.export');

    // Logout
    Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');
});

// Google OAuth & OTP Verification Routes
Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

    // OTP 2FA Verification Routes
    Route::get('/auth/otp', [GoogleController::class, 'showOtpForm'])->name('auth.otp.show');
    Route::post('/auth/otp', [GoogleController::class, 'verifyOtp'])->name('auth.otp.verify');
    Route::post('/auth/otp/resend', [GoogleController::class, 'resendOtp'])->name('auth.otp.resend');
});
