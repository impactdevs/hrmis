<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = DB::table('attendances')
            ->select(
                'staff_id',
                'access_date',
                DB::raw('MIN(access_date_and_time) as clock_in'),
                DB::raw('MAX(access_date_and_time) as clock_out')
            )
            ->groupBy('staff_id', 'access_date');

        if ($request->filled('filter_date')) {
            $query->whereDate('access_date', $request->filter_date);
        }

        if ($request->filled('department_id')) {
            $staffIds = Employee::where('department_id', $request->department_id)->pluck('staff_id');
            $query->whereIn('staff_id', $staffIds);
        }

        $summarizedAttendances = $query->paginate(10);

        return view('attendances.index', compact('summarizedAttendances'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        return view('attendances.show', compact('attendance'));
    }

    public function store_api(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|string',
            'access_date_and_time' => 'required|date',
        ]);

        $datetime = date('Y-m-d H:i:s', strtotime($validated['access_date_and_time']));

        $attendance = Attendance::create([
            'attendance_id' => Str::uuid(),
            'staff_id' => $validated['staff_id'],
            'access_date_and_time' => $datetime,
            'access_date' => date('Y-m-d', strtotime($datetime)),
            'access_time' => date('H:i:s', strtotime($datetime)),
        ]);

        return response()->json([
            'message' => 'Attendance recorded successfully',
            'data' => $attendance
        ], 201);
    }
}
