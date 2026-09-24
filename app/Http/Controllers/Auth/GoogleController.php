<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpCodeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google's OAuth page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if existing user has a manually uploaded local avatar
            $existingUser = User::where('google_id', $googleUser->getId())->first();
            $hasLocalAvatar = $existingUser && $existingUser->avatar && str_starts_with($existingUser->avatar, '/uploads/');

            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    // Only update avatar from Google if user doesn't have a locally uploaded one
                    'avatar' => $hasLocalAvatar ? $existingUser->avatar : $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                ]
            );

            // Generate 6-digit OTP code for OAuth 2FA verification
            $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store pending user authentication in session
            session([
                'pending_user_id' => $user->id,
                'pending_user_name' => $user->name,
                'pending_user_email' => $user->email,
                'otp_code' => $otpCode,
                'otp_expires_at' => now()->addMinutes(10)->timestamp,
            ]);

            Log::info("OTP generated for OAuth login ({$user->email}): {$otpCode}");

            // Send real OTP Email
            try {
                Mail::to($user->email)->send(new OtpCodeMail($otpCode, $user->name));
            } catch (\Exception $mailEx) {
                Log::error("Failed sending OTP email: " . $mailEx->getMessage());
            }

            return redirect()->route('auth.otp.show')
                ->with('info', "Kode OTP telah dikirimkan ke email Anda ({$user->email}). Silakan periksa inbox / log Anda.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Google OAuth error: " . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'Google login failed. Please try again.');
        }
    }

    /**
     * Display the OTP verification form.
     */
    public function showOtpForm(Request $request)
    {
        if (!session('pending_user_id') || !session('otp_code')) {
            return redirect()->route('home')->with('error', 'Sesi verifikasi OTP tidak ditemukan atau sudah kadaluwarsa.');
        }

        return view('auth.otp', [
            'email' => session('pending_user_email'),
            'name' => session('pending_user_name'),
            'otpCode' => session('otp_code'),
        ]);
    }

    /**
     * Verify the entered OTP code.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $userId = session('pending_user_id');
        $storedOtp = session('otp_code');
        $expiresAt = session('otp_expires_at');

        if (!$userId || !$storedOtp) {
            return redirect()->route('home')->with('error', 'Sesi verifikasi OTP telah berakhir.');
        }

        if (now()->timestamp > $expiresAt) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluwarsa. Silakan kirim ulang kode baru.']);
        }

        $inputOtp = implode('', (array) $request->input('otp_digits', []));
        if (empty($inputOtp)) {
            $inputOtp = (string) $request->input('otp');
        }

        if ($inputOtp !== $storedOtp) {
            return back()->withErrors(['otp' => 'Kode OTP salah. Silakan periksa kembali.']);
        }

        // OTP verified successfully — complete login
        $user = User::findOrFail($userId);
        Auth::login($user, remember: true);

        // Clear OTP session variables
        session()->forget(['pending_user_id', 'pending_user_name', 'pending_user_email', 'otp_code', 'otp_expires_at']);

        return redirect()->route('dashboard')->with('success', 'Verifikasi OTP berhasil! Selamat datang, ' . $user->name . '!');
    }

    /**
     * Resend a new OTP code.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $userId = session('pending_user_id');
        if (!$userId) {
            return redirect()->route('home')->with('error', 'Sesi berakhir.');
        }

        $newOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        session([
            'otp_code' => $newOtp,
            'otp_expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        Log::info("Resent OTP ({$userId}): {$newOtp}");

        $userEmail = session('pending_user_email');
        $userName = session('pending_user_name');
        try {
            if ($userEmail) {
                Mail::to($userEmail)->send(new OtpCodeMail($newOtp, $userName ?? 'User'));
            }
        } catch (\Exception $mailEx) {
            Log::error("Failed resending OTP email: " . $mailEx->getMessage());
        }

        return back()->with('success', "Kode OTP baru telah dikirimkan ke email Anda.");
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }
}
