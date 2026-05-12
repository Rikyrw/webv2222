<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

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

            $response = $this->supabaseRequest('post', '/rest/v1/admin', $payload, true);

            if (!$response->successful()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menambahkan admin'
                ], 500);
            }
            
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

            $response = $this->supabaseRequest(
                'patch',
                '/rest/v1/admin?id_admin=eq.' . $validated['id_admin'],
                $payload,
                true
            );

            if (!$response->successful()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengupdate admin'
                ], 500);
            }
            
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

            $response = $this->supabaseRequest(
                'delete',
                '/rest/v1/admin?id_admin=eq.' . $id,
                null,
                true
            );

            if (!$response->successful()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menghapus admin'
                ], 500);
            }
            
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
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/admin?select=id_admin,nama_lengkap,email,role,user_name,username:user_name,status,no_hp,alamat',
            null,
            false
        );

        if (!$response->successful()) {
            return [];
        }

        $admins = $response->json();
        return is_array($admins) ? $admins : [];
    }

    private function adminExistsByEmail(string $email): bool
    {
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/admin?select=id_admin&email=eq.' . urlencode($email) . '&limit=1',
            null,
            false
        );

        if (!$response->successful()) {
            return false;
        }

        $admins = $response->json();
        return is_array($admins) && count($admins) > 0;
    }

    private function adminExistsByUsername(string $username): bool
    {
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/admin?select=id_admin&user_name=eq.' . urlencode($username) . '&limit=1',
            null,
            false
        );

        if (!$response->successful()) {
            return false;
        }

        $admins = $response->json();
        return is_array($admins) && count($admins) > 0;
    }

    private function adminExistsById($id): bool
    {
        if (empty($id)) {
            return false;
        }

        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/admin?select=id_admin&id_admin=eq.' . $id . '&limit=1',
            null,
            false
        );

        if (!$response->successful()) {
            return false;
        }

        $admins = $response->json();
        return is_array($admins) && count($admins) > 0;
    }

    private function supabaseRequest(string $method, string $path, ?array $payload, bool $returnRepresentation)
    {
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_KEY');

        $request = Http::withHeaders([
            'apikey' => $supabaseKey,
            'Authorization' => 'Bearer ' . $supabaseKey,
        ]);

        if ($returnRepresentation) {
            $request = $request->withHeaders([
                'Prefer' => 'return=representation',
            ]);
        }

        $url = $supabaseUrl . $path;

        if ($method === 'get') {
            return $request->get($url);
        }

        if ($method === 'post') {
            return $request->post($url, $payload ?? []);
        }

        if ($method === 'patch') {
            return $request->patch($url, $payload ?? []);
        }

        if ($method === 'delete') {
            return $request->delete($url);
        }

        return $request->get($url);
    }
}
