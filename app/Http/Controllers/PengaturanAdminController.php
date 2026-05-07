<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class PengaturanAdminController extends Controller
{
    public function index()
    {
        // Check if user is superadmin
        if (!auth()->check() || auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized access');
        }

        $activePage = 'pengaturan';
        $pageTitle = 'Pengaturan Admin';
        
        // Get admin data from database
        $admins = AdminUser::select(
            'id_admin',
            'nama_lengkap',
            'email',
            'role',
            'user_name as username',
            'status',
            'no_hp',
            'alamat'
        )
        ->get()
        ->toArray();
        
        return view('admin.pengaturan_admin', compact(
            'activePage',
            'pageTitle',
            'admins'
        ));
    }

    public function store(Request $request)
    {
        // Check if superadmin
        if (!auth()->check() || auth()->user()->role !== 'superadmin') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $action = $request->input('action');
        
        if ($action === 'add') {
            // Create new admin
            $validated = $request->validate([
                'username' => 'required|string|unique:admin,user_name',
                'nama_lengkap' => 'required|string',
                'email' => 'required|email|unique:admin,email',
                'password' => 'required|string|min:6',
                'role' => 'required|in:operator,admin,superadmin',
                'no_hp' => 'nullable|string',
                'alamat' => 'nullable|string',
            ]);
            
            // Create in database
            AdminUser::create([
                'user_name' => $validated['username'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'status' => 'aktif',
                'no_hp' => $validated['no_hp'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Admin berhasil ditambahkan'
            ]);
        } 
        elseif ($action === 'edit') {
            // Update admin
            $validated = $request->validate([
                'id_admin' => 'required|integer|exists:admin,id_admin',
                'username' => 'required|string',
                'nama_lengkap' => 'required|string',
                'email' => 'required|email',
                'password' => 'nullable|string|min:6',
                'role' => 'required|in:operator,admin,superadmin',
                'status' => 'required|in:aktif,nonaktif',
                'no_hp' => 'nullable|string',
                'alamat' => 'nullable|string',
            ]);
            
            // Update in database
            $admin = AdminUser::find($validated['id_admin']);
            if ($admin) {
                $admin->update([
                    'user_name' => $validated['username'],
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'email' => $validated['email'],
                    'role' => $validated['role'],
                    'status' => $validated['status'],
                    'no_hp' => $validated['no_hp'] ?? null,
                    'alamat' => $validated['alamat'] ?? null,
                ]);
                
                if ($validated['password']) {
                    $admin->update(['password' => Hash::make($validated['password'])]);
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Admin berhasil diupdate'
            ]);
        }
        elseif ($action === 'delete') {
            // Delete admin
            $id = $request->input('id_admin');
            AdminUser::find($id)?->delete();
            
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
}
