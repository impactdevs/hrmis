<?php

namespace App\Http\Controllers;

use App\Models\OffDesk;
use App\Models\Employee;
use Illuminate\Http\Request;

class OffDeskController extends Controller
{
    public function index()
    {
        $entries = OffDesk::with('employee')->paginate(10);
        return view('offdesk.index', compact('entries'));
    }

    public function create()
    {
        return view('offdesk.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after_or_equal:start_datetime',
            'destination'         => 'nullable|string|max:100',
            'duty_allocated'         => 'nullable|string|max:100',
            'reason'         => 'nullable|string|max:1000',
        ]);

        // Fetch employee ID from logged-in user
        $employeeId = auth()->user()->employee->employee_id ?? null;

        if (!$employeeId) {
            return back()->with('error', 'Employee record not found for the current user.');
        }

        // Merge employee ID into data
        $validated['employee_id'] = $employeeId;

        OffDesk::create($validated);

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

        $validated = $request->validate([
            'employee_id'    => 'required|exists:employees,id',
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
