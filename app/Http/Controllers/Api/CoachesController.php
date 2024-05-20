<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coaches;
use Illuminate\Http\Request;
use Validator;

class CoachesController extends Controller
{
    public function index()
    {
        $coaches = Coaches::all();
        if($coaches->count() > 0){
            return response()->json([
                'code' => 200,
                'data'=> $coaches,
                'status'=> 'success'
            ],200);
        }else{
            return response()->json([
                'code' => 404,
                'data' => $coaches,
                'status'=> 'No Record Found'
            ],404);
        }
    }

    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(),[
        //     'nama'=>
        //     'jenis_kelamin',
        //     'tanggal_lahir',
        //     'pengalaman_tahun',
        //     'lokasi_kerja',
        //     'email'
        // ])

        $coaches = Coaches::create([
            'nama'=> $request->nama,
            'jenis_kelamin' => $request->kelamin,
            'tanggal_lahir' => $request->tgl_lahir,
            'pengalaman_tahun'=> $request->pengalaman_tahun,
            'lokasi_kerja'=> $request->lokasi_kerja,
            'email'=> $request->email
        ]);

        if($coaches){
            return response()->json([
                'code'=> 200,
                'message'=> ' Created Successfully',
                'status' => 'success'
            ],200);
        }else{
            return response()->json([
                'code'=> 500,
                'message'=> ' Created Unsuccessfully',
                'status' => 'failed'
            ],200);
        }
    }
}
