<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Programs;
use Illuminate\Http\Request;

use Illuminate\Support\Str;

class ProgramsController extends Controller
{   

    public function index()
    {
        $programs = Programs::all();
        if ($programs->count() > 0) {
            return response()->json([
                'code' => 200,
                'data' => $programs,
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'data' => $programs,
                'status' => 'No Record Found'
            ], 404);
        }
    }
    public function store(Request $request)
    {   
        // Periksa apakah id spesialis diberikan dalam request
        if ($request->has('id') && $request->id) {
            // Mencari entri spesialis berdasarkan id
            $programs = Programs::find($request->id);

            if ($programs) {
                // Jika entri spesialis ditemukan, lakukan update
                $programs->program = $request->program;
                $programs->price = 0;
                $programs->desc = $request->desc;
                $operation = 'updated';
            } else {
                return response()->json(['message' => 'Spesialis not found'], 404);
            }
        } else {
            // Jika id tidak diberikan atau tidak ditemukan, buat entri spesialis baru
            $programs = new Programs();
            $programs->id = (string) Str::uuid();
            $programs->program = $request->program;
            $programs->price = 0;
            $programs->desc = $request->desc;
            $operation = 'created';
        }

        if ($programs->save()) {
            return response()->json([
                'code' => 200,
                'message' => "{$operation} successfully",
                'data' => [
                    'id' => $programs->id
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
}
