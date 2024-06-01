<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ScheduleDetail;
use App\Models\ScheduleMember;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function trainings()
    {
        $schedules = DB::table('fit_db.schedule AS s')
            ->leftJoin('fit_db.schedule_detail AS sd', 's.id', '=', 'sd.id_schedule')
            ->leftJoin('fit_db.program AS p', 'p.id', '=', 's.id_program')
            ->leftJoin('fit_db.trainer AS t', 't.id', '=', 's.id_trainer')
            ->leftJoin('fit_db.users AS u', 'u.id', '=', 't.id_user')
            ->select(
                's.id',
                'u.name',
                'p.program',
                's.id AS schedule_id',
                's.id_trainer',
                's.id_program',
                DB::raw('MIN(sd.date_schedule) AS start_date'),
                DB::raw('MAX(sd.date_schedule) AS end_date'),
                DB::raw('COUNT(sd.id_schedule) AS total_days'),
                's.created_at',
                's.updated_at',
                's.is_active'
            )
            ->groupBy(
                's.id',
                's.id_trainer',
                's.id_program',
                's.total_days',
                's.created_at',
                's.updated_at',
                's.is_active',
                'u.name',
                'p.program'
            )
            ->get();

        return response()->json([
            'code' => 200,
            'data' => $schedules,
            'status' => 'success'
        ], 200);
    }
    public function schedule($id)
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $schedule = Schedule::where('id', '=', $id)
            ->first();

        if (!$schedule) {
            return response()->json(['message' => 'schedule not found'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $schedule,
            'status' => 'success'
        ], 200);
    }

    public function store(Request $request)
    {
        // Periksa apakah id  diberikan dalam request
        if ($request->has('id') && $request->id) {
            // Mencari entri  berdasarkan id
            $schedule = Schedule::find($request->id);

            if ($schedule) {
                // Jika entri  ditemukan, lakukan update
                $schedule->id_trainer = $request->id_trainer;
                $schedule->id_program = $request->id_program;
                $schedule->desc = $request->desc;
                $operation = 'updated';
            } else {
                return response()->json(['message' => ' not found'], 404);
            }
        } else {
            // Jika id tidak diberikan atau tidak ditemukan, buat entri baru
            $schedule = new Schedule();
            $schedule->id = (string) Str::uuid();
            $schedule->id_trainer = $request->id_trainer;
            $schedule->id_program = $request->id_program;
            $schedule->desc = $request->desc;
            $operation = 'created';
        }

        if ($schedule->save()) {
            return response()->json([
                'code' => 200,
                'message' => "{$operation} successfully",
                'data' => [
                    'id' => $schedule->id
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

    public function members($id)
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $schedule = Schedule::rightJoin('schedule_member', 'schedule.id', '=', 'schedule_member.id_schedule')
            ->leftJoin('client', 'client.id', '=', 'schedule_member.id_client')
            ->leftJoin('users', 'users.id', '=', 'client.id_user')
            ->select(
                'users.name',
                'schedule_member.id',
                'schedule_member.id_client',
                'jenis_kelamin',
                'tanggal_lahir',
            )
            ->where('schedule.id', '=', $id)
            ->get();

        if (!$schedule) {
            return response()->json(['message' => 'schedule not found'], 404);
        }

        if ($schedule->isEmpty()) {
            return response()->json(['message' => 'schedule not found'], 404);
        } else {
            $schedule->transform(function ($item) {
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
        }

        // Transform the results


        return response()->json([
            'code' => 200,
            'data' => $schedule,
            'status' => 'success'
        ], 200);
    }

    public function storeMember(Request $request)
    {
        // Periksa apakah id  diberikan dalam request
        if ($request->has('id') && $request->id) {
            // Mencari entri  berdasarkan id
            $scheduleMember = ScheduleMember::find($request->id);

            if ($scheduleMember) {
                // Jika entri  ditemukan, lakukan update
                $scheduleMember->id_schedule = $request->id_schedule;
                $scheduleMember->id_client = $request->id_client;
                $operation = 'updated';
            } else {
                return response()->json(['message' => ' not found'], 404);
            }
        } else {
            // Jika id tidak diberikan atau tidak ditemukan, buat entri baru
            $scheduleMember = new ScheduleMember();
            $scheduleMember->id = (string) Str::uuid();
            $scheduleMember->id_schedule = $request->id_schedule;
            $scheduleMember->id_client = $request->id_client;
            $operation = 'created';
        }

        if ($scheduleMember->save()) {
            return response()->json([
                'code' => 200,
                'message' => "{$operation} successfully",
                'data' => [
                    'id' => $scheduleMember->id
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

    public function detail($id)
    {
        // Lakukan query untuk mendapatkan data pengguna berdasarkan ID
        $schedule = ScheduleDetail::where('id_schedule', '=', $id)
            ->get();

        if (!$schedule) {
            return response()->json(['message' => 'schedule not found'], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $schedule,
            'status' => 'success'
        ], 200);
    }

    public function storeDetail(Request $request)
    {
        // Periksa apakah id  diberikan dalam request
        if ($request->has('id') && $request->id) {
            // Mencari entri  berdasarkan id
            $scheduleDetail = ScheduleDetail::find($request->id);

            if ($scheduleDetail) {
                // Jika entri  ditemukan, lakukan update
                $scheduleDetail->id_schedule = $request->id_schedule;
                $scheduleDetail->location = $request->location;
                $scheduleDetail->date_schedule = $request->date_schedule;
                $scheduleDetail->time_start = $request->time_start;
                $scheduleDetail->time_end = $request->time_end;
                $operation = 'updated';
            } else {
                return response()->json(['message' => ' not found'], 404);
            }
        } else {
            // Jika id tidak diberikan atau tidak ditemukan, buat entri baru
            $scheduleDetail = new ScheduleDetail();
            $scheduleDetail->id = (string) Str::uuid();
            $scheduleDetail->id_schedule = $request->id_schedule;
            $scheduleDetail->location = $request->location;
            $scheduleDetail->date_schedule = $request->date_schedule;
            $scheduleDetail->time_start = $request->time_start;
            $scheduleDetail->time_end = $request->time_end;
            $operation = 'created';
        }

        if ($scheduleDetail->save()) {
            return response()->json([
                'code' => 200,
                'message' => "{$operation} successfully",
                'data' => [
                    'id' => $scheduleDetail->id
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
