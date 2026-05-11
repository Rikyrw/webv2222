<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminUser;

class AdminAuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('admin.login');
    }

    // Handle login submission
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'role' => 'required|in:admin,superadmin',
        ]);

        // Attempt login using admin guard
        if (Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $admin = Auth::guard('admin')->user();
            
            // Check if selected role matches database role
            if ($admin->role !== $data['role']) {
                Auth::guard('admin')->logout();
                return redirect()->back()->withInput()->with('error', 'Role yang dipilih tidak cocok dengan akun Anda.');
            }
            
            session()->put('admin_logged_in', true);
            session()->put('admin_email', $admin->email);
            session()->put('admin_role', $admin->role);
            session()->put('admin_id', $admin->id_admin);
            return redirect()->route('admin.dashboard')->with('success', 'Login berhasil.');
        }

        return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
    }

    // Handle logout
    public function logout()
    {
        Auth::guard('admin')->logout();
        session()->forget(['admin_logged_in', 'admin_email', 'admin_role', 'admin_id']);
        return redirect()->route('admin.login.form')->with('success', 'Logout berhasil.');
    }
}
