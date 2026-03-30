<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // Default to current month if no dates provided
        $dateFrom = $request->filled('date_from')
            ? $request->date_from
            : now()->startOfMonth()->format('Y-m-d');

        $dateTo = $request->filled('date_to')
            ? $request->date_to
            : now()->endOfMonth()->format('Y-m-d');

        $query = DB::table('attendances')
            ->select(
                'staff_id',
                'access_date',
                DB::raw('MIN(access_date_and_time) as clock_in'),
                DB::raw('MAX(access_date_and_time) as clock_out'),
                DB::raw('TIMEDIFF(MAX(access_date_and_time), MIN(access_date_and_time)) as hours_worked')
            )
            ->whereBetween('access_date', [$dateFrom, $dateTo])
            ->groupBy('staff_id', 'access_date')
            ->orderBy('access_date', 'desc');

        // Filter by department — HR only
        if ($request->filled('department_id') && auth()->user()->hasRole('HR')) {
            $staffIds = Employee::where('department_id', $request->department_id)
                ->pluck('staff_id');
            $query->whereIn('staff_id', $staffIds);
        }

        // Filter by specific employee
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        $summarizedAttendances = $query->paginate(25)->withQueryString();

        $departments = Department::orderBy('department_name')->get();
        $employees   = Employee::orderBy('first_name')->get();

        return view('attendances.index', compact(
            'summarizedAttendances',
            'departments',
            'employees',
            'dateFrom',
            'dateTo'
        ));
    }

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

        // Normalize staff_id: keep prefix, zero-pad the number to 3 digits
        // e.g. AP35 → AP035, AP048 → AP048 (already 3), SP51 → SP051
        $staffId = preg_replace_callback('/^([A-Za-z]+)(\d+)$/', function ($m) {
            return strtoupper($m[1]) . str_pad($m[2], 3, '0', STR_PAD_LEFT);
        }, trim($validated['staff_id']));

        // Verify employee exists before recording
        $employee = Employee::where('staff_id', $staffId)->first();
        if (!$employee) {
            return response()->json([
                'message' => "No employee found for device ID: {$validated['staff_id']} (normalized: {$staffId})"
            ], 404);
        }

        $datetime = date('Y-m-d H:i:s', strtotime($validated['access_date_and_time']));

        $attendance = Attendance::create([
            'attendance_id' => Str::uuid(),
            'staff_id' => $staffId, // store the normalized version
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
