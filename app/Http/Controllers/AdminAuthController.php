<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        try {
            $adminModel = AdminUser::where('email', $data['email'])->first();

            if (!$adminModel) {
                return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
            }

            $admin = $adminModel->getAttributes();
            $loginSuccess = $this->verifyPasswordLikeAndroid(
                $data['password'],
                $admin['password'] ?? null,
                $admin['salt'] ?? null
            );

            if (!$loginSuccess) {
                if (isset($admin['password']) && password_verify($data['password'], $admin['password'])) {
                    $loginSuccess = true;
                } elseif (isset($admin['password']) && $data['password'] === $admin['password']) {
                    $loginSuccess = true;
                }
            }

            if (!$loginSuccess) {
                return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
            }

            if (($admin['role'] ?? null) !== $data['role']) {
                return redirect()->back()->withInput()->with('error', 'Role yang dipilih tidak cocok dengan akun Anda.');
            }

            session()->put('admin_logged_in', true);
            session()->put('admin_email', $admin['email'] ?? null);
            session()->put('admin_role', $admin['role'] ?? null);
            session()->put('admin_id', $admin['id_admin'] ?? null);

            return redirect()->route('admin.dashboard')->with('success', 'Login berhasil.');
        } catch (\Throwable $e) {
            Log::error('Admin login failed unexpectedly.', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with('error', $this->loginSystemErrorMessage($e));
        }
    }

    // Handle logout
    public function logout()
    {
        Auth::guard('admin')->logout();
        session()->forget(['admin_logged_in', 'admin_email', 'admin_role', 'admin_id']);
        return redirect()->route('admin.login.form')->with('success', 'Logout berhasil.');
    }

    /**
     * Verifikasi password dengan metode Android (SHA256 + Salt + Base64)
     */
    private function verifyPasswordLikeAndroid($inputPassword, $storedHash, $salt)
    {
        if (empty($salt)) {
            return false;
        }

        try {
            $saltBinary = base64_decode($salt);
            $hashedInput = hash('sha256', $saltBinary . $inputPassword, true);
            $hashedInputBase64 = base64_encode($hashedInput);

            return $hashedInputBase64 === $storedHash;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function loginSystemErrorMessage(\Throwable $e): string
    {
        $message = $e->getMessage();

        if (
            str_contains($message, 'SQLSTATE[08006]')
            || str_contains($message, 'could not translate host name')
            || str_contains($message, 'No such host is known')
            || str_contains($message, 'Network is unreachable')
            || str_contains($message, 'Connection refused')
        ) {
            return 'Database belum bisa dijangkau. Periksa koneksi database/pooler Supabase, lalu coba lagi.';
        }

        return 'Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.';
    }
}
