<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clients;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function trainerClient($id)
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $user = Clients::leftJoin('users', 'users.id', '=', 'client.id_user')
            ->leftJoin('schedule_member', 'schedule_member.id_client', '=', 'client.id')
            ->leftJoin('schedule', 'schedule_member.id_schedule', '=', 'schedule.id')
            ->select(
                'users.name',
                'client.*',
            )
            ->where('schedule.id_trainer', '=', $id)
            ->get();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        } 

        $user->transform(function ($item) {
            // Convert jenis_kelamin
            $item->jenis_kelamin = $item->jenis_kelamin == 'p' ? 'Pria' : 'Wanita';

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
}
