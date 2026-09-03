<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminAuthController extends Controller
{
    public function showLogin(): Response
    {
        return Inertia::render('Admin/Login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|string']);

        $hash = config('app.admin_password_hash');

        if (! $hash || ! Hash::check($request->string('password'), $hash)) {
            throw ValidationException::withMessages([
                'password' => 'Şifre hatalı.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('is_admin', true);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('is_admin');
        $request->session()->regenerate();

        return redirect()->route('home');
    }
}
