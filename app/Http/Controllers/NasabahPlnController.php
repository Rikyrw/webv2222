<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class NasabahPlnController extends Controller
{
    public function index(Request $request)
    {
        // Design-only: don't call backend. Provide a minimal $user array like emoney.
        $user = [
            'saldo' => session('saldo') ?? 0,
        ];
        $pln_error = Session::get('pln_error', '');

        return view('nasabah.pln', compact('user', 'pln_error'));
    }

    public function store(Request $request)
    {
        // Design-only store: validate input but do not call backend.
        $request->validate([
            'target' => 'required|string|max:255',
            'nominal' => 'required|integer|min:5000|max:100000',
        ]);

        return redirect()->back()->with('success', 'Mode desain: form divalidasi, tidak ada pemanggilan backend.');
    }
}