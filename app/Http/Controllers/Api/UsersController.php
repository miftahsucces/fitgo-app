<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        // $request->validate([
        //     'fullName' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|string|min:8|confirmed',
        //     'tipe_anggota' => 'required|string|max:255',
        //     'jenis_kelamin' => 'required|string|max:1',
        //     'tanggal_lahir' => 'required|date',
        //     'tinggi_badan' => 'required|numeric',
        //     'berat_badan' => 'required|numeric',
        //     'golongan_darah' => 'required|string|max:2',
        //     'alamat' => 'required|string|max:255',
        //     'telepon' => 'nullable|string|max:15',
        //     'about_me' => 'nullable|string',
        //     'profile_foto' => 'nullable|string',
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
        $user->id = (string) Str::uuid();
        $user->name = $request->fullName;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            // Create the user detail
            $userDetail = new UsersDetail();
            $userDetail->id = (string) Str::uuid();
            $userDetail->id_user = $user->id;
            $userDetail->id_member = $request->memberId;
            $userDetail->tipe_anggota = $request->memberType;
            $userDetail->jenis_kelamin = $request->gender;
            $userDetail->tanggal_lahir = $request->dob;
            $userDetail->tinggi_badan = $request->height;
            $userDetail->berat_badan = $request->weight;
            $userDetail->golongan_darah = $request->bloodType;
            $userDetail->alamat = $request->address;
            // $userDetail->telepon = $request->telepon;
            // $userDetail->about_me = $request->about_me;
            // $userDetail->profile_foto = $request->profile_foto;

            if ($userDetail->save()) {
                return response()->json([
                    'code' => 200,
                    'message' => 'User and User Detail created successfully',
                    'data' => [
                        'user' => $user,
                        'user_detail' => $userDetail
                    ],
                    'status' => 'success'
                ], 200);
            } else {
                return response()->json([
                    'code' => 500,
                    'message' => 'Failed to create user detail',
                    'status' => 'failed'
                ], 500);
            }
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to create user',
                'status' => 'failed'
            ], 500);
        }
    }
    
}
