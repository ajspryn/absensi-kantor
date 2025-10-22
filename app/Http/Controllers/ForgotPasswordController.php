<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordResetRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'reason' => 'required|string|max:500'
        ]);

        // Check if user already has pending request
        $existingRequest = PasswordResetRequest::where('email', $request->email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingRequest) {
            return back()->withErrors([
                'email' => 'Anda sudah memiliki permintaan reset password yang sedang menunggu persetujuan admin.'
            ]);
        }

        // Create new reset request
        PasswordResetRequest::create([
            'email' => $request->email,
            'token' => Str::random(60),
            'status' => 'pending',
            'reason' => $request->reason,
            'expires_at' => now()->addDays(7), // Expires in 7 days
        ]);

        return redirect()->route('login')->with(
            'success',
            'Permintaan reset password telah dikirim ke admin. Silakan tunggu persetujuan.'
        );
    }

    public function showResetForm($token)
    {
        $resetRequest = PasswordResetRequest::where('token', $token)
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetRequest) {
            return redirect()->route('login')->withErrors([
                'email' => 'Token reset password tidak valid atau sudah kedaluwarsa.'
            ]);
        }

        return view('auth.reset-password', compact('resetRequest'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $resetRequest = PasswordResetRequest::where('token', $request->token)
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetRequest) {
            return back()->withErrors([
                'token' => 'Token reset password tidak valid atau sudah kedaluwarsa.'
            ]);
        }

        // Update user password
        $user = User::where('email', $resetRequest->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Mark request as used
        $resetRequest->update(['status' => 'used']);

        return redirect()->route('login')->with(
            'success',
            'Password berhasil direset. Silakan login dengan password baru.'
        );
    }
}
