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
        if ($users->count() > 0) {
            return response()->json([
                'code' => 200,
                'data' => $users,
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'data' => $users,
                'status' => 'No Record Found'
            ], 404);
        }
    }

    public function users($id)
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $user = User::leftJoin('users_detail', 'users.id', '=', 'users_detail.id_user')
            ->select(
                'users.id',
                'name',
                'email',
                'id_user',
                'id_member',
                'tipe_anggota',
                'jenis_kelamin',
                'tanggal_lahir',
                'tinggi_badan',
                'berat_badan',
                'golongan_darah',
                'alamat',
                'telepon',
                'about_me',
                'profile_foto'
            )
            ->where('users.id', '=', $id)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $user,
            'status' => 'success'
        ], 200);
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
            $userDetail->id_member = $this->generateId($request->memberType);
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

    public function update(Request $request)
    {
        // Validasi data jika diperlukan
        // $request->validate([
        //     'fullName' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        //     'password' => 'nullable|string|min:8|confirmed',
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

        // Ambil data pengguna yang akan diupdate
        $id = $request->id;
        $user = User::findOrFail($id);

        // Perbarui data pengguna dengan nilai baru dari permintaan
        $user->name = $request->fullName;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        // Simpan perubahan ke dalam database
        if ($user->save()) {
            // Ambil data user detail yang terkait dengan pengguna
            $userDetail = UsersDetail::where('id_user', $id)->first();

            // Perbarui data user detail dengan nilai baru dari permintaan
            // $userDetail->id_member = $this->generateId($request->memberType);
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

            // Simpan perubahan data user detail ke dalam database
            if ($userDetail->save()) {
                return response()->json([
                    'code' => 200,
                    'message' => 'User and User Detail updated successfully',
                    'data' => [
                        'user' => $user,
                        'user_detail' => $userDetail
                    ],
                    'status' => 'success'
                ], 200);
            } else {
                return response()->json([
                    'code' => 500,
                    'message' => 'Failed to update user detail',
                    'status' => 'failed'
                ], 500);
            }
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to update user',
                'status' => 'failed'
            ], 500);
        }
    }

    function generateId($idType)
    {
        // Buat variabel untuk menyimpan prefix sesuai dengan tipe anggota
        $prefix = '';

        // Tentukan prefix berdasarkan tipe anggota
        switch ($idType) {
            case '1': //admin
                $prefix = 'AD';
                break;
            case '2': //Personal Trainer
                $prefix = 'PT';
                break;
            case '3': //client
                $prefix = 'CN';
                break;
            default:
                // Handle default case
                break;
        }

        // Ambil 3 digit terakhir tahun
        $tahun = substr(date('Y'), -3);

        // Hitung jumlah data users_detail berdasarkan tipe anggota
        $increment = UsersDetail::where('tipe_anggota', $idType)->count() + 1;

        // Format increment menjadi 4 digit dengan leading zero
        $incrementFormatted = sprintf('%04d', $increment);

        // Gabungkan prefix, tahun, dan increment untuk membentuk id_member
        $idMember = $prefix . $tahun . $incrementFormatted;

        return $idMember;
    }
}
