<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('admin.login');
    }

    // Handle login submission (simple stub)
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'role' => 'required|string',
        ]);

        // Very small demo auth: replace with real auth (Supabase) integration
        $email = strtolower($data['email']);
        $password = $data['password'];
        $role = strtolower($data['role']);

        // Demo rule: password must be 'password' and email contains role keyword
        if ($password === 'password' && str_contains($email, $role)) {
            session()->put('admin_logged_in', true);
            session()->put('admin_email', $email);
            session()->put('admin_role', $role);
            return redirect()->route('admin.dashboard')->with('success', 'Login berhasil.');
        }

        return redirect()->back()->withInput()->with('error', 'Email atau password salah, atau role tidak cocok.');
    }
}
