<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::all();
        if($users->count() > 0){
            return response()->json([
                'code' => 200,
                'data'=> $users,
                'status'=> 'success'
            ],200);
        }else{
            return response()->json([
                'code' => 404,
                'data' => $users,
                'status'=> 'No Record Found'
            ],404);
        }
    }

    public function store(Request $request)
    {
        // Validasi data jika diperlukan
        // $validator = Validator::make($request->all(),[
        //     'name' => 'required',
        //     'email' => 'required|email|unique:users',
        //     'password' => 'required|min:6',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'code' => 400,
        //         'message' => 'Validation error',
        //         'errors' => $validator->errors(),
        //         'status' => 'failed'
        //     ], 400);
        // }

        // Buat user baru
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        
        if ($user->save()) {
            return response()->json([
                'code' => 200,
                'message' => 'User created successfully',
                'data' => $user,
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
}
