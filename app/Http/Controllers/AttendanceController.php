<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
            ->orderBy('access_date', 'desc')
            ->orderBy('clock_in', 'desc');

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

    public function export(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? $request->date_from
            : now()->startOfMonth()->format('Y-m-d');

        $dateTo = $request->filled('date_to')
            ? $request->date_to
            : now()->endOfMonth()->format('Y-m-d');

        // *** Must use the SAME grouped DB::table query as index() ***
        // Using Attendance::with(...) would fetch individual scans, not daily summaries
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
            ->orderBy('access_date', 'desc')
            ->orderBy('clock_in', 'desc');

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

        $rows = $query->get();

        // Build employee lookup keyed by staff_id — done once, outside any closure
        $uniqueStaffIds = $rows->pluck('staff_id')->unique()->values();
        $employeeMap    = Employee::with('department')
            ->whereIn('staff_id', $uniqueStaffIds)
            ->get()
            ->keyBy('staff_id');

        // Pre-process everything into plain arrays before streaming/rendering
        // This avoids Eloquent lazy-loading and Carbon issues inside closures
        $processedRows = $rows->map(function ($row) use ($employeeMap) {
            $emp = $employeeMap->get($row->staff_id);
            return [
                'staff_id'     => $row->staff_id,
                'first_name'   => $emp ? $emp->first_name : 'N/A',
                'last_name'    => $emp ? $emp->last_name  : 'N/A',
                'department'   => ($emp && $emp->department) ? $emp->department->department_name : 'N/A',
                'date'         => $row->access_date
                                    ? date('d-m-Y', strtotime((string) $row->access_date))
                                    : 'N/A',
                'clock_in'     => $row->clock_in
                                    ? date('H:i:s', strtotime((string) $row->clock_in))
                                    : 'N/A',
                'clock_out'    => $row->clock_out
                                    ? date('H:i:s', strtotime((string) $row->clock_out))
                                    : 'N/A',
                'hours_worked' => $row->hours_worked ?? 'N/A',
            ];
        })->toArray();

        $format = $request->get('format', 'csv');

        return $format === 'pdf'
            ? $this->exportPdf($processedRows, $dateFrom, $dateTo)
            : $this->exportCsv($processedRows, $dateFrom, $dateTo);
    }

    private function exportCsv(array $processedRows, string $dateFrom, string $dateTo)
    {
        $filename = "attendance_{$dateFrom}_to_{$dateTo}.csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // Only plain arrays go into the closure — no Eloquent, no Carbon
        $callback = function () use ($processedRows) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Staff ID', 'First Name', 'Last Name', 'Department',
                'Date', 'Clock In', 'Clock Out', 'Hours Worked',
            ]);

            foreach ($processedRows as $row) {
                fputcsv($handle, [
                    $row['staff_id'],
                    $row['first_name'],
                    $row['last_name'],
                    $row['department'],
                    $row['date'],
                    $row['clock_in'],
                    $row['clock_out'],
                    $row['hours_worked'],
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportPdf(array $processedRows, string $dateFrom, string $dateTo)
    {
        $filename = "attendance_{$dateFrom}_to_{$dateTo}.pdf";

        $html = view('attendances.export-pdf', [
            'processedRows' => $processedRows,
            'dateFrom'      => $dateFrom,
            'dateTo'        => $dateTo,
            'totalRecords'  => count($processedRows),
        ])->render();

        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => false]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return Response::make($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function show(Attendance $attendance)
    {
        return view('attendances.show', compact('attendance'));
    }

    public function store_api(Request $request)
    {
        $validated = $request->validate([
            'staff_id'             => 'required|string',
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
            'attendance_id'        => Str::uuid(),
            'staff_id'             => $staffId,
            'access_date_and_time' => $datetime,
            'access_date'          => date('Y-m-d', strtotime($datetime)),
            'access_time'          => date('H:i:s',  strtotime($datetime)),
        ]);

        return response()->json([
            'message' => 'Attendance recorded successfully',
            'data'    => $attendance,
        ], 201);
    }
}