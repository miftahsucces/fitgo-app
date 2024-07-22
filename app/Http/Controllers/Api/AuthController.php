<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Mail\TestMail;
use App\Mail\ForgotPasswordEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Patients;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // $request->validate([
        //     'name' => 'required',
        //     'email' => 'required|unique:users,email',
        //     'password' => 'required|confirmed'
        // ]);

        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => $request->first_name,
            'email' => $request->email,
            'tipe_user' => '2',
            'password' => bcrypt($request->password)
        ]);

        if ($user) {
            return response()->json([
                'code' => 200,
                'message' => 'User and User Detail created successfully',
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to create user',
                'status' => 'failed'
            ], 500);
        }

    }

    public function login(Request $request)
    {
        // $request->validate([
        //     'email' => 'required',
        //     'password' => 'required'
        // ]);

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'Invalid Credentials',
                'error_code' => 401
            ], 401);
        }

        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('access_token', ['user']);

        return response()->json([
            'message' => 'Login success',
            'user' => $user,
            'access_token' => $token->plainTextToken
        ]);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user();
        return response()->json($user);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logout success'
        ], 200);
    }

    public function testMail(Request $request)
    {
        $data = [
            'name' => 'Joe Doe',
            'body' => "this is a test message"
        ];
        Mail::to('test@gmail.com')->send(new TestMail('test subject', $data));
    }

    public function forgetPasswordRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'errors' => ['email' => ['Account with this email not found.']]
            ], 422);
        }

        $code = rand(11111, 99999);

        $user->remember_token = $code;
        $user->save();

        $data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'code' => $code,
        ];

        Mail::to($user->email)->send(new ForgotPasswordEmail('Forgot Password Request', $data));

        return response()->json([
            'message' => 'We have sended code to your email.'
        ]);
    }

    public function verifyAndChangePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|integer',
            'password' => 'required|confirmed',
        ]);

        $user = User::where('email', $request->email)
            ->where('remember_token', $request->code)
            ->first();

        if (!$user) {
            return response()->json([
                'errors' => ['code' => ['Invalid otp']]
            ], 422);
        }

        $user->remember_token = null;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password successfull changed.'
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed',
        ]);

        $user = $request->user();

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully.'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
        ]);

        $user = $request->user();

        if ($user->email != $request->email) {
            $request->validate([
                'email' => 'required|unique:users,email'
            ]);
            $user->email = $request->email;
        }

        $user->first_name = $request->first_name ?? $user->first_name;
        $user->last_name = $request->last_name ?? $user->last_name;

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => $user
        ]);
    }

    function generateId()
    {
        // Define the prefix
        $prefix = 'PS';
        // Get the last two digits of the current year
        $tahun = substr(date('Y'), -2);

        // Get the current month with leading zero
        $bulan = date('m');

        // Get the count of trainers created in the current month and year
        $increment = User::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count() + 1;

        // Format increment as a 4-digit number with leading zeros
        $incrementFormatted = sprintf('%04d', $increment);

        // Concatenate prefix, year, month, and formatted increment to create the ID
        $idMember = $prefix . $tahun . $bulan . $incrementFormatted;

        return $idMember;
    }
}
