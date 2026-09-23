<?php

namespace App\Http\Controllers;

use App\Models\OffDesk;
use App\Models\Employee;
use App\Notifications\OffDeskNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class OffDeskController extends Controller
{
    public function index()
    {
        $query = OffDesk::with('employee');

        // HR files these on an employee's behalf, so HR sees everyone's;
        // everyone else only sees their own.
        if (!auth()->user()->hasRole('HR')) {
            $employeeId = auth()->user()->employee->employee_id ?? null;
            $employeeId ? $query->where('employee_id', $employeeId) : $query->whereRaw('1 = 0');
        }

        $entries = $query->paginate(10);
        return view('offdesk.index', compact('entries'));
    }

    public function create()
    {
        // Only active employees — you wouldn't schedule this for someone
        // who's left.
        $employees = Employee::active()->get();
        return view('offdesk.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'    => 'required|exists:employees,employee_id',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after_or_equal:start_datetime',
            'destination'         => 'nullable|string|max:100',
            'duty_allocated'         => 'nullable|string|max:100',
            'reason'         => 'nullable|string|max:1000',
        ]);

        // HR is filing this on behalf of the selected employee.
        $offdesk = OffDesk::create($validated);

        // Let the employee know HR scheduled this for them.
        $offdesk->loadMissing('employee.user');
        $recipient = $offdesk->employee?->user;
        if ($recipient) {
            Notification::send($recipient, new OffDeskNotification(
                $offdesk,
                $offdesk->employee->first_name,
                $offdesk->employee->last_name
            ));
        }

        return redirect()->route('offdesk.index')->with('success', 'Off desk record created successfully.');
    }

    public function show($id)
    {
        $entry = OffDesk::with('employee')->findOrFail($id);
        return view('offdesk.show', compact('entry'));
    }

    public function edit($id)
    {
        $offdesk = OffDesk::findOrFail($id);
        // $employees = Employee::all();
        return view('offdesk.edit', compact('offdesk'));
    }

    public function update(Request $request, $id)
    {
        $entry = OffDesk::findOrFail($id);

        // employee_id isn't on this form and shouldn't be editable here — it's
        // set once by HR at creation (see store()) and never reassigned.
        $validated = $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after_or_equal:start_datetime',
            'destination'         => 'required|string|max:100',
            'duty_allocated'         => 'required|string|max:100',
            'reason'         => 'required|string|max:1000',
        ]);

        $entry->update($validated);

        return redirect()->route('offdesk.index')->with('success', 'Off desk record updated successfully.');
    }

    public function destroy($id)
    {
        $entry = OffDesk::findOrFail($id);
        $entry->delete();

        return redirect()->route('offdesk.index')->with('success', 'Off desk record deleted.');
    }
}
