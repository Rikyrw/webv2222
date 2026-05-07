<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class NasabahPulsaController extends Controller
{
    public function index(Request $request)
    {
        // Design-only: don't call backend. Provide a minimal $user array similar to emoney view.
        $user = [
            'saldo' => session('saldo') ?? 0,
        ];

        // Pass an empty error message by default so the view can render design state
        $pulsa_error = Session::get('pulsa_error', '');

        return view('nasabah.pulsa', compact('user', 'pulsa_error'));
    }

    public function store(Request $request)
    {
        // Design-only store: validate input but do not call backend. Show a design notice.
        $request->validate([
            'target' => 'required|string|max:255',
            'nominal' => 'required|integer|min:5000|max:100000',
        ]);

        // Optionally simulate a success message but do not change any backend state.
        return redirect()->back()->with('success', 'Mode desain: form divalidasi, tidak ada pemanggilan backend.');
    }
}
