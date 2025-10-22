<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee; // Get employee from user relationship

        // Get monthly statistics
        $currentMonth = now()->startOfMonth();
        $nextMonth = now()->addMonth()->startOfMonth();

        $monthlyStats = [
            'present' => $employee->attendances()
                ->whereBetween('date', [$currentMonth, $nextMonth])
                ->whereNotNull('check_in')
                ->whereNotNull('check_out')
                ->count(),
            'absent' => $employee->attendances()
                ->whereBetween('date', [$currentMonth, $nextMonth])
                ->whereNull('check_in')
                ->count(),
            'leave' => 0 // You can implement leave system later
        ];

        return view('employee.profile', compact('employee', 'monthlyStats'));
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            $employee = $user->employee;

            $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email,' . $employee->id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $data = $request->only(['full_name', 'email', 'phone', 'address']);

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($employee->photo) {
                    Storage::disk('public')->delete($employee->photo);
                }

                // Store new photo
                $photoPath = $request->file('photo')->store('employee_photos', 'public');
                $data['photo'] = $photoPath;
            }

            // Update employee data
            foreach ($data as $key => $value) {
                $employee->{$key} = $value;
            }
            $employee->save();

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'current_password' => 'required',
                'new_password' => ['required', 'confirmed', Password::min(8)],
            ]);

            // Check if current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini tidak benar'
                ], 422);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
