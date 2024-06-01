<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = User::where('email', '=', $request->email)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $additionalData = [
            'id' => $user->id,
            'email' => $request->email,
            'name' => $user->name,
            'role_user' => $user->tipe_user,
            // Add other user data as needed
        ];

        // return response()->noContent();
        return response()->json($additionalData);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
