<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Coaches;
use Illuminate\Http\Request;
use Validator;

use App\Models\User;
use App\Models\Trainer;
use App\Models\Spesialis;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CoachesController extends Controller
{
    public function index()
    {
        $coaches = Coaches::all();
        if ($coaches->count() > 0) {
            return response()->json([
                'code' => 200,
                'data' => $coaches,
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'data' => $coaches,
                'status' => 'No Record Found'
            ], 404);
        }
    }

    public function coaches($id)
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $user = User::leftJoin('trainer', 'users.id', '=', 'trainer.id_user')
            ->select(
                'trainer.id',
                'users.tipe_user',
                'name',
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

        // Buat user baru
        $user = new User();
        $user->id = (string) Str::uuid();
        $user->name = $request->fullName;
        $user->email = $request->email;
        $user->tipe_user = '2'; //1 :admin, 2 : trainer, 3 : client
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            $trainer = new Trainer();
            $trainer->id = $this->generateIdPt();
            $trainer->id_user = $user->id;
            $trainer->email2 = '';
            $trainer->jenis_kelamin = $request->gender;
            $trainer->tanggal_lahir = $request->dob;
            $trainer->tinggi_badan = $request->height;
            $trainer->berat_badan = $request->weight;
            $trainer->golongan_darah = $request->bloodType;
            $trainer->alamat = $request->address;
            $trainer->telepon = $request->telepon;
            // $trainer->about_me = $request->about_me;
            // $trainer->profile_foto = $request->profile_foto;

            if ($trainer->save()) {
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
            $user->name = $request->fullName;
            $user->email = $request->email;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            if ($user->save()) {
                // Find or create a trainer associated with the user
                $trainer = Trainer::where('id_user', $user->id)->first();

                if (!$trainer) {
                    // If no trainer exists, create a new one
                    $trainer = new Trainer();
                    $trainer->id = $this->generateIdPt();
                    $trainer->id_user = $user->id;
                }

                // Update trainer details
                $trainer->email2 = $request->email2 ?? $trainer->email2;
                $trainer->jenis_kelamin = $request->gender;
                $trainer->tanggal_lahir = $request->dob;
                $trainer->tinggi_badan = $request->height;
                $trainer->berat_badan = $request->weight;
                $trainer->golongan_darah = $request->bloodType;
                $trainer->alamat = $request->address;
                $trainer->telepon = $request->telepon;
                // $trainer->about_me = $request->about_me;
                // $trainer->profile_foto = $request->profile_foto;

                if ($trainer->save()) {
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


    public function spesialis($id)
    {
        $spesialis = Trainer::rightJoin('trainer_spesialis', 'trainer.id', '=', 'trainer_spesialis.id_trainer')
            ->leftJoin('users', 'users.id', '=', 'trainer.id_user')
            ->select(
                'trainer_spesialis.id',
                'spesialis',
            )
            ->where('users.id', '=', $id)
            ->orderBy('trainer_spesialis.created_at', 'desc') // Tambahkan order by
            ->get();

        if (!$spesialis) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $spesialis,
            'status' => 'success'
        ], 200);
    }

    public function storeSpesialis(Request $request)
    {
        // Mengambil id_trainer dari tabel trainer berdasarkan id_user
        $id_user = $request->id_user;
        $trainer = Trainer::where('id_user', $id_user)->first();

        if (!$trainer) {
            return response()->json(['message' => 'Trainer not found'], 404);
        }

        // Periksa apakah id spesialis diberikan dalam request
        if ($request->has('id') && $request->id) {
            // Mencari entri spesialis berdasarkan id
            $spesialis = Spesialis::find($request->id);

            if ($spesialis) {
                // Jika entri spesialis ditemukan, lakukan update
                $spesialis->spesialis = $request->spesialis;
                $operation = 'updated';
            } else {
                return response()->json(['message' => 'Spesialis not found'], 404);
            }
        } else {
            // Jika id tidak diberikan atau tidak ditemukan, buat entri spesialis baru
            $spesialis = new Spesialis();
            $spesialis->id = (string) Str::uuid();
            $spesialis->id_trainer = $trainer->id;
            $spesialis->spesialis = $request->spesialis;
            $operation = 'created';
        }

        if ($spesialis->save()) {
            return response()->json([
                'code' => 200,
                'message' => "Spesialis {$operation} successfully",
                'data' => [
                    'id' => $spesialis->id
                ],
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'message' => "Failed to {$operation} Spesialis",
                'status' => 'failed'
            ], 500);
        }
    }

    public function destroySpesialis($id)
    {
        // Mencari entri spesialis berdasarkan id
        $spesialis = Spesialis::find($id);

        if (!$spesialis) {
            return response()->json([
                'code' => 404,
                'message' => 'Spesialis not found',
                'status' => 'failed'
            ], 404);
        }

        // Menghapus entri spesialis
        if ($spesialis->delete()) {
            return response()->json([
                'code' => 200,
                'message' => 'Spesialis deleted successfully',
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to delete Spesialis',
                'status' => 'failed'
            ], 500);
        }
    }

    public function certi($id)
    {
        $certification = Trainer::rightJoin('trainer_certification', 'trainer.id', '=', 'trainer_certification.id_trainer')
            ->leftJoin('users', 'users.id', '=', 'trainer.id_user')
            ->select(
                'trainer_certification.id',
                'id_trainer',
                'organization',
                'program',
                'year',
                'location',
                'desc',
            )
            ->where('users.id', '=', $id)
            ->orderBy('trainer_certification.created_at', 'desc') // Tambahkan order by
            ->get();

        if (!$certification) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $certification,
            'status' => 'success'
        ], 200);
    }


    public function storeCert(Request $request)
    {
        // Mengambil id_trainer dari tabel trainer berdasarkan id_user
        $id_user = $request->id_user;
        $trainer = Trainer::where('id_user', $id_user)->first();

        if (!$trainer) {
            return response()->json(['message' => 'Trainer not found'], 404);
        }

        // Periksa apakah id spesialis diberikan dalam request
        if ($request->has('id') && $request->id) {
            // Mencari entri spesialis berdasarkan id
            $certification = Certification::find($request->id);

            if ($certification) {
                // Jika entri spesialis ditemukan, lakukan update
                $certification->organization = $request->organization;
                $certification->program = $request->program;
                $certification->year = $request->year;
                $certification->location = $request->location;
                $operation = 'updated';
            } else {
                return response()->json(['message' => 'Spesialis not found'], 404);
            }
        } else {
            // Jika id tidak diberikan atau tidak ditemukan, buat entri spesialis baru
            $certification = new Certification();
            $certification->id = (string) Str::uuid();
            $certification->id_trainer = $trainer->id;
            $certification->organization = $request->organization;
            $certification->program = $request->program;
            $certification->year = $request->year;
            $certification->location = $request->location;
            $operation = 'created';
        }

        if ($certification->save()) {
            return response()->json([
                'code' => 200,
                'message' => "Spesialis {$operation} successfully",
                'data' => [
                    'id' => $certification->id
                ],
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'message' => "Failed to {$operation} Spesialis",
                'status' => 'failed'
            ], 500);
        }
    }

    public function destroyCerti($id)
    {
        // Mencari entri spesialis berdasarkan id
        $certification = Certification::find($id);

        if (!$certification) {
            return response()->json([
                'code' => 404,
                'message' => 'certification not found',
                'status' => 'failed'
            ], 404);
        }

        // Menghapus entri spesialis
        if ($certification->delete()) {
            return response()->json([
                'code' => 200,
                'message' => 'certification deleted successfully',
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to delete certification',
                'status' => 'failed'
            ], 500);
        }
    }
    function generateIdPt()
    {
        // Define the prefix
        $prefix = 'PT';

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
