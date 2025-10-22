<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PasswordResetRequest;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PasswordResetApproved;

class PasswordResetController extends Controller
{
    public function index()
    {
        $resetRequests = PasswordResetRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.password-reset.index', compact('resetRequests'));
    }

    public function approve($id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);

        if (!$resetRequest->isPending()) {
            return back()->withErrors(['error' => 'Permintaan ini sudah diproses sebelumnya.']);
        }

        $resetRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Notify the user that their reset request was approved
        $user = $resetRequest->user;
        if ($user) {
            $user->notify(new PasswordResetApproved($resetRequest));
        }

        return back()->with('success', 'Permintaan reset password telah disetujui.');
    }

    public function reject($id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);

        if (!$resetRequest->isPending()) {
            return back()->withErrors(['error' => 'Permintaan ini sudah diproses sebelumnya.']);
        }

        $resetRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Permintaan reset password telah ditolak.');
    }
}
