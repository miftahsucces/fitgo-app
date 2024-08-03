<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clients;
use Illuminate\Http\Request;
use App\Models\Body;
use App\Models\Sick;
use App\Models\Fitness;
use App\Models\Daily;
use Illuminate\Support\Str;

class ProgressController extends Controller
{
    public function trainerClient($id)
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $user = Clients::leftJoin('users', 'users.id', '=', 'client.id_user')
            ->leftJoin('schedule_member', 'schedule_member.id_client', '=', 'client.id_user')
            ->leftJoin('schedule', 'schedule_member.id_schedule', '=', 'schedule.id')
            ->distinct()
            ->select(
                'users.full_name',
                'client.*',
            )
            ->where('schedule.id_trainer', '=', $id)
            ->get();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->transform(function ($item) {
            // Convert jenis_kelamin
            if ($item->jenis_kelamin == 'male') {
                $item->jenis_kelamin = 'Laki-laki';
            } elseif ($item->jenis_kelamin == 'female') {
                $item->jenis_kelamin = 'Wanita';
            }

            // Calculate age from tanggal_lahir
            $birthDate = new \DateTime($item->tanggal_lahir);
            $currentDate = new \DateTime();
            $age = $currentDate->diff($birthDate)->y;

            // Add age to the item
            $item->umur = $age;

            return $item;
        });

        return response()->json([
            'code' => 200,
            'data' => $user,
            'status' => 'success'
        ], 200);
    }

    public function storeBody(Request $request)
    {
        // Periksa apakah id  diberikan dalam request
        if ($request->has('id') && $request->id) {
            // Mencari entri  berdasarkan id
            $body = Body::find($request->id);

            if ($body) {
                // Jika entri  ditemukan, lakukan update
                $body->id_client = $request->id_client;
                $body->result_day = $request->result_day;
                $body->weigth = $request->weigth;
                $body->body_fat = $request->body_fat;
                $body->body_water = $request->body_water;
                $body->muscle_mass = $request->muscle_mass;
                $body->physical_rating = $request->physical_rating;
                $body->bmr = $request->bmr;
                $body->metabolic_age = $request->metabolic_age;
                $body->bone_mass = $request->bone_mass;
                $body->visceral_fat = $request->visceral_fat;
                $body->date_actual = $request->date_actual;
                $operation = 'updated';
            } else {
                return response()->json(['message' => ' not found'], 404);
            }
        } else {
            // Jika id tidak diberikan atau tidak ditemukan, buat entri baru
            $body = new Body();
            $body->id = (string) Str::uuid();
            $body->id_client = $request->id_client;
            $body->result_day = $request->result_day;
            $body->weigth = $request->weigth;
            $body->body_fat = $request->body_fat;
            $body->body_water = $request->body_water;
            $body->muscle_mass = $request->muscle_mass;
            $body->physical_rating = $request->physical_rating;
            $body->bmr = $request->bmr;
            $body->metabolic_age = $request->metabolic_age;
            $body->bone_mass = $request->bone_mass;
            $body->visceral_fat = $request->visceral_fat;
            $body->date_actual = $request->date_actual;
            $operation = 'created';
        }

        if ($body->save()) {
            return response()->json([
                'code' => 200,
                'message' => "{$operation} successfully",
                'data' => [
                    'id' => $body->id
                ],
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'message' => "Failed to {$operation}",
                'status' => 'failed'
            ], 500);
        }
    }

    public function getBody($id)
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $body = Body::where('id_client',  $id)
            ->get();

        if (!$body) {
            return response()->json(['message' => 'Body not found'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $body,
            'status' => 'success'
        ], 200);
    }

    public function storeSick(Request $request)
    {
        // Periksa apakah id  diberikan dalam request
        if ($request->has('id') && $request->id) {
            // Mencari entri  berdasarkan id
            $body = Sick::find($request->id);

            if ($body) {
                // Jika entri  ditemukan, lakukan update
                $body->id_client = $request->id_client;
                $body->desc = $request->desc;
                $operation = 'updated';
            } else {
                return response()->json(['message' => ' not found'], 404);
            }
        } else {
            // Jika id tidak diberikan atau tidak ditemukan, buat entri baru
            $body = new Sick();
            $body->id = (string) Str::uuid();
            $body->id_client = $request->id_client;
            $body->desc = $request->desc;
            $operation = 'created';
        }

        if ($body->save()) {
            return response()->json([
                'code' => 200,
                'message' => "{$operation} successfully",
                'data' => [
                    'id' => $body->id
                ],
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'message' => "Failed to {$operation}",
                'status' => 'failed'
            ], 500);
        }
    }

    public function getSick($id)
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $body = Sick::where('id_client',  $id)
            ->get();

        if (!$body) {
            return response()->json(['message' => 'Body not found'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $body,
            'status' => 'success'
        ], 200);
    }

    public function storeFitness(Request $request)
    {
        // Periksa apakah id  diberikan dalam request
        if ($request->has('id') && $request->id) {
            // Mencari entri  berdasarkan id
            $body = Fitness::find($request->id);

            if ($body) {
                // Jika entri  ditemukan, lakukan update
                $body->id_client = $request->id_client;
                $body->desc = $request->desc;
                $operation = 'updated';
            } else {
                return response()->json(['message' => ' not found'], 404);
            }
        } else {
            // Jika id tidak diberikan atau tidak ditemukan, buat entri baru
            $body = new Fitness();
            $body->id = (string) Str::uuid();
            $body->id_client = $request->id_client;
            $body->desc = $request->desc;
            $operation = 'created';
        }

        if ($body->save()) {
            return response()->json([
                'code' => 200,
                'message' => "{$operation} successfully",
                'data' => [
                    'id' => $body->id
                ],
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'message' => "Failed to {$operation}",
                'status' => 'failed'
            ], 500);
        }
    }

    public function getFitness($id)
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $body = Fitness::where('id_client',  $id)
            ->get();

        if (!$body) {
            return response()->json(['message' => 'Body not found'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $body,
            'status' => 'success'
        ], 200);
    }

    public function storeDaily(Request $request)
    {
        // Periksa apakah id  diberikan dalam request
        if ($request->has('id') && $request->id) {
            // Mencari entri  berdasarkan id
            $body = Daily::find($request->id);

            if ($body) {
                // Jika entri  ditemukan, lakukan update
                $body->id_client = $request->id_client;
                $body->desc = $request->desc;
                $operation = 'updated';
            } else {
                return response()->json(['message' => ' not found'], 404);
            }
        } else {
            // Jika id tidak diberikan atau tidak ditemukan, buat entri baru
            $body = new Daily();
            $body->id = (string) Str::uuid();
            $body->id_client = $request->id_client;
            $body->desc = $request->desc;
            $operation = 'created';
        }

        if ($body->save()) {
            return response()->json([
                'code' => 200,
                'message' => "{$operation} successfully",
                'data' => [
                    'id' => $body->id
                ],
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'code' => 500,
                'message' => "Failed to {$operation}",
                'status' => 'failed'
            ], 500);
        }
    }

    public function getDaily($id)
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $body = Daily::where('id_client',  $id)
            ->get();

        if (!$body) {
            return response()->json(['message' => 'Body not found'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $body,
            'status' => 'success'
        ], 200);
    }

    public function deleteDaily($id)
    {
        $body = Daily::find($id);

        if ($body) {
            // Jika entri ditemukan, lakukan penghapusan
            if ($body->delete()) {
                return response()->json([
                    'code' => 200,
                    'message' => "Deleted successfully",
                    'status' => 'success'
                ], 200);
            } else {
                return response()->json([
                    'code' => 500,
                    'message' => "Failed to delete",
                    'status' => 'failed'
                ], 500);
            }
        } else {
            return response()->json([
                'code' => 404,
                'message' => "Not found",
                'status' => 'failed'
            ], 404);
        }
    }

    public function deleteBody($id)
    {
        $body = Body::find($id);

        if ($body) {
            // Jika entri ditemukan, lakukan penghapusan
            if ($body->delete()) {
                return response()->json([
                    'code' => 200,
                    'message' => "Deleted successfully",
                    'status' => 'success'
                ], 200);
            } else {
                return response()->json([
                    'code' => 500,
                    'message' => "Failed to delete",
                    'status' => 'failed'
                ], 500);
            }
        } else {
            return response()->json([
                'code' => 404,
                'message' => "Not found",
                'status' => 'failed'
            ], 404);
        }
    }

    public function deleteSick($id)
    {
        $body = Sick::find($id);

        if ($body) {
            // Jika entri ditemukan, lakukan penghapusan
            if ($body->delete()) {
                return response()->json([
                    'code' => 200,
                    'message' => "Deleted successfully",
                    'status' => 'success'
                ], 200);
            } else {
                return response()->json([
                    'code' => 500,
                    'message' => "Failed to delete",
                    'status' => 'failed'
                ], 500);
            }
        } else {
            return response()->json([
                'code' => 404,
                'message' => "Not found",
                'status' => 'failed'
            ], 404);
        }
    }

    public function deleteFitness($id)
    {
        $body = Fitness::find($id);

        if ($body) {
            // Jika entri ditemukan, lakukan penghapusan
            if ($body->delete()) {
                return response()->json([
                    'code' => 200,
                    'message' => "Deleted successfully",
                    'status' => 'success'
                ], 200);
            } else {
                return response()->json([
                    'code' => 500,
                    'message' => "Failed to delete",
                    'status' => 'failed'
                ], 500);
            }
        } else {
            return response()->json([
                'code' => 404,
                'message' => "Not found",
                'status' => 'failed'
            ], 404);
        }
    }
}
