<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Clients;
use App\Models\Coaches;
use Illuminate\Http\Request;
use Validator;

use App\Models\User;
use App\Models\Trainer;
use App\Models\Spesialis;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;

class ClientsController extends Controller
{
    public function index()
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $user = User::rightJoin('client', 'users.id', '=', 'client.id_user')
            ->select(
                'client.id',
                'users.tipe_user',
                'full_name',
                'email',
                'id_user',
                'jenis_kelamin',
                'tanggal_lahir',
                'tinggi_badan',
                'berat_badan',
                'golongan_darah',
                'alamat',
                'telepon',
                'about_me',
                'profile_foto',
                'aktifitas',
                'tujuan',
                'medis',
                DB::raw("CASE 
                    WHEN jenis_kelamin = 'male' THEN 'Lelaki' 
                    WHEN jenis_kelamin = 'female' THEN 'Perempuan' 
                    ELSE jenis_kelamin 
                 END AS gender"),
                DB::raw("
                    TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) AS umur"),
            )
            ->get();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $user,
            'status' => 'success'
        ], 200);
    }

    public function clients($id)
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $user = User::leftJoin('client', 'users.id', '=', 'client.id_user')
            ->select(
                'client.id',
                'users.tipe_user',
                'full_name',
                'email',
                'id_user',
                'jenis_kelamin',
                'tanggal_lahir',
                'tinggi_badan',
                'berat_badan',
                'golongan_darah',
                'alamat',
                'telepon',
                'about_me',
                'profile_foto',
                'aktifitas',
                'tujuan',
                'medis',
                DB::raw("
                CASE 
                    WHEN jenis_kelamin = 'p' THEN 'Male'
                    WHEN jenis_kelamin = 'w' THEN 'Female'
                    ELSE 'Unknown'
                END AS gender_label
                "),
                    DB::raw("
                    TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) AS umur_tahun
                "),
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

        // Buat user baru
        $user = new User();
        $user->id = (string) Str::uuid();
        $user->full_name = $request->fullName;
        $user->email = $request->email;
        $user->tipe_user = '3'; //1 :admin, 2 : trainer, 3 : client
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            $client = new Clients();
            $client->id = $this->generateIdCn();
            $client->id_user = $user->id;
            $client->email2 = '';
            $client->jenis_kelamin = $request->gender;
            $client->tanggal_lahir = $request->dob;
            $client->tinggi_badan = $request->height;
            $client->berat_badan = $request->weight;
            $client->golongan_darah = $request->bloodType;
            $client->alamat = $request->address;
            $client->telepon = $request->phoneNumber;
            $client->aktifitas = $request->dailyActivity;
            $client->tujuan = $request->fitnessGoals;
            $client->medis = $request->medicalHistory;
            // $trainer->profile_foto = $request->profile_foto;

            if ($client->save()) {
                return response()->json([
                    'code' => 200,
                    'message' => 'User and User Detail created successfully',
                    'data' => [
                        'id' => $user->id
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
        // Find the user by id
        $id = $request->inputIdUser;
        $user = User::find($id);

        if ($user) {
            // Update user details
            $user->full_name = $request->fullName;
            $user->email = $request->email;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            if ($user->save()) {
                // Find or create a trainer associated with the user
                $client = Clients::where('id_user', $user->id)->first();

                if (!$client) {
                    // If no trainer exists, create a new one
                    $client = new Clients();
                    $client->id = $this->generateIdCn();
                    $client->id_user = $user->id;
                }

                // Update trainer details
                $client->email2 = $request->email2 ?? $client->email2;
                $client->jenis_kelamin = $request->gender;
                $client->tanggal_lahir = $request->dob;
                $client->tinggi_badan = $request->height;
                $client->berat_badan = $request->weight;
                $client->golongan_darah = $request->bloodType;
                $client->alamat = $request->address;
                $client->telepon = $request->phoneNumber;
                $client->aktifitas = $request->dailyActivity;
                $client->tujuan = $request->fitnessGoals;
                $client->medis = $request->medicalHistory;
                // $trainer->about_me = $request->about_me;
                // $trainer->profile_foto = $request->profile_foto;

                if ($client->save()) {
                    return response()->json([
                        'code' => 200,
                        'message' => 'User and User Detail updated successfully',
                        'data' => [
                            'id' => $user->id
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
        } else {
            return response()->json([
                'code' => 404,
                'message' => 'User not found',
                'status' => 'failed'
            ], 404);
        }
    }


    function generateIdCn()
    {
        // Define the prefix
        $prefix = 'CN';

        // Get the last two digits of the current year
        $tahun = substr(date('Y'), -2);

        // Get the current month with leading zero
        $bulan = date('m');

        // Get the count of trainers created in the current month and year
        $increment = Trainer::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count() + 1;

        // Format increment as a 4-digit number with leading zeros
        $incrementFormatted = sprintf('%04d', $increment);

        // Concatenate prefix, year, month, and formatted increment to create the ID
        $idMember = $prefix . $tahun . $bulan . $incrementFormatted;

        return $idMember;
    }
}
