<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PengaturanAdminController extends Controller
{
    public function index()
    {
        $activePage = 'pengaturan';
        $pageTitle = 'Pengaturan Admin';

        $admins = $this->fetchAdmins();

        return view('admin.pengaturan_admin', compact(
            'activePage',
            'pageTitle',
            'admins'
        ));
    }

    public function store(Request $request)
    {
        $action = $request->input('action');
        
        if ($action === 'add') {
            // Create new admin
            $validated = $request->validate([
                'username' => 'required|string',
                'nama_lengkap' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'role' => 'required|in:operator,admin,superadmin',
                'no_hp' => 'nullable|string',
                'alamat' => 'nullable|string',
            ]);

            if ($this->adminExistsByEmail($validated['email'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email sudah terdaftar'
                ], 422);
            }

            if ($this->adminExistsByUsername($validated['username'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Username sudah terdaftar'
                ], 422);
            }

            $payload = [
                'user_name' => $validated['username'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'status' => 'aktif',
                'no_hp' => $validated['no_hp'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
            ];

            AdminUser::create($payload);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Admin berhasil ditambahkan'
            ]);
        } 
        elseif ($action === 'edit') {
            // Update admin
            $validated = $request->validate([
                'id_admin' => 'required|integer',
                'username' => 'required|string',
                'nama_lengkap' => 'required|string',
                'email' => 'required|email',
                'password' => 'nullable|string|min:6',
                'role' => 'required|in:operator,admin,superadmin',
                'status' => 'required|in:aktif,nonaktif',
                'no_hp' => 'nullable|string',
                'alamat' => 'nullable|string',
            ]);

            if (!$this->adminExistsById($validated['id_admin'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Admin tidak ditemukan'
                ], 404);
            }

            $payload = [
                'user_name' => $validated['username'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'status' => $validated['status'],
                'no_hp' => $validated['no_hp'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $payload['password'] = Hash::make($validated['password']);
            }

            AdminUser::where('id_admin', $validated['id_admin'])->update($payload);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Admin berhasil diupdate'
            ]);
        }
        elseif ($action === 'delete') {
            // Delete admin
            $id = $request->input('id_admin');

            if (!$this->adminExistsById($id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Admin tidak ditemukan'
                ], 404);
            }

            AdminUser::where('id_admin', $id)->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Admin berhasil dihapus'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid action'
        ]);
    }

    private function fetchAdmins(): array
    {
        return AdminUser::orderBy('id_admin')
            ->get(['id_admin', 'nama_lengkap', 'email', 'role', 'user_name', 'status', 'no_hp', 'alamat'])
            ->map(function (AdminUser $admin): array {
                $row = $admin->toArray();
                $row['username'] = $row['user_name'] ?? null;

                return $row;
            })
            ->all();
    }

    private function adminExistsByEmail(string $email): bool
    {
        return AdminUser::where('email', $email)->exists();
    }

    private function adminExistsByUsername(string $username): bool
    {
        return AdminUser::where('user_name', $username)->exists();
    }

    private function adminExistsById($id): bool
    {
        if (empty($id)) {
            return false;
        }

        return AdminUser::where('id_admin', $id)->exists();
    }
}
