<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;

class ProfileController extends Controller
{
    /**
     * Display the admin profile page.
     */
    public function showAdmin(): Response
    {
        $user = Auth::user();
        return Inertia::render('Auth/Profile/Admin', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'status' => session('status'),
        ]);
    }

    /**
     * Display the employee profile page.
     */
    public function showEmployee(): Response
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->with('checker', 'company')->first();

        $employeeData = $employee ? [
            'emp_id' => $employee->emp_id,
            'name' => $employee->name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'company_id' => $employee->company ? $employee->company->name : null,
            'role' => $employee->role,
            'designation' => $employee->designation,
            'status' => $employee->status,
            'is_self_checker' => $employee->is_self_checker,
            'checker' => $employee->is_self_checker ? 'Self Checker' : ($employee->checker ? $employee->checker->name : null),
        ] : null;

        return Inertia::render('Auth/Profile/Employee', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'employee' => $employeeData,
            'status' => session('status'),
        ]);
    }

    /**
     * Handle password update request.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['error' => 'Current password is incorrect']);
        }

        Log::info('Password updated for user', ['user_id' => $user->id]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Password updated successfully, please log in again');
    }
}