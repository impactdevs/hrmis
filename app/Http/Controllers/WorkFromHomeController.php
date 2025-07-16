<?php

namespace App\Http\Controllers;

use App\Models\WorkFromHome;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class WorkFromHomeController extends Controller
{
    public function index()
    {
        $entries = WorkFromHome::with('employee')->paginate(10);
        return view('workfromhome.index', compact('entries'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('workfromhome.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_from_home_start_date' => 'required|date',
            'work_from_home_end_date'   => 'required|date|after_or_equal:work_from_home_start_date',
            'work_from_home_reason'     => 'required|string|max:1000',
            'work_from_home_attachments'=> 'nullable|mimes:pdf,jpg,jpeg,png,doc,docx,xlsx|max:10240',
            'work_location'             => 'required|string|max:100',

            // task fields
            'task_start_date.*' => 'required|date',
            'task_end_date.*'   => 'required|date|after_or_equal:task_start_date.*',
            'description.*'     => 'required|string|max:1000',
        ]);

        if ($request->hasFile('work_from_home_attachments')) {
            $validated['work_from_home_attachments'] = $request->file('work_from_home_attachments')->store('attachments', 'public');
        }

        // Employee ID from logged in user
        $employeeId = auth()->user()->employee->employee_id ?? null;
        if (!$employeeId) {
            return back()->with('error', 'Employee record not found for the current user.');
        }

        DB::beginTransaction();

        try {
            // Create work from home record
            $workFromHome = WorkFromHome::create([
                ...$validated,
                'employee_id' => $employeeId,
            ]);

            if ($request->has('task_start_date')) {
    foreach ($request->task_start_date as $index => $start) {
        $workFromHome->task()->create([
            'task_start_date' => $start,
            'task_end_date'   => $request->task_end_date[$index],
            'description'     => $request->description[$index],
        ]);
    }
}

            DB::commit();

            return redirect()->route('workfromhome.index')->with('success', 'Work from home request created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save request: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $entry = WorkFromHome::with('employee')->findOrFail($id);
        return view('workfromhome.show', compact('entry'));
    }

    public function edit($id)
    {
       $entry = WorkFromHome::with('task')->findOrFail($id);
        $employees = Employee::all();
        return view('workfromhome.edit', compact('entry', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $entry = WorkFromHome::findOrFail($id);

        $validated = $request->validate([
            'employee_id'                 => 'required|exists:employees,id',
            'work_from_home_start_date'   => 'required|date',
            'work_from_home_end_date'     => 'required|date|after_or_equal:work_from_home_start_date',
            'work_location'             => 'required|string|max:100',
            'work_from_home_reason'       => 'required|string|max:1000',
            'work_from_home_attachments'  => 'nullable|file|max:10240',

        ]);

        if ($request->hasFile('work_from_home_attachments')) {
            $validated['work_from_home_attachments'] = $request->file('work_from_home_attachments')->store('attachments', 'public');
        }

        $entry->update($validated);

        return redirect()->route('workfromhome.index')->with('success', 'Work from home request updated successfully.');
    }

    public function destroy($id)
    {
        $entry = WorkFromHome::findOrFail($id);
        $entry->delete();

        return redirect()->route('workfromhome.index')->with('success', 'Work from home request deleted.');
    }

    public function approve($id)
    {
        $entry = WorkFromHome::findOrFail($id);

        if (!auth()->user()->hasRole('HR')) {
            abort(403);
        }

        $entry->update(['status' => 'approved']);

        return redirect()->route('workfromhome.show', $id)->with('success', 'Request approved.');
    }

    public function decline(Request $request, $id)
    {
        $entry = WorkFromHome::findOrFail($id);

        if (!auth()->user()->hasRole('HR')) {
            abort(403);
        }

        $request->validate([
            'decline_reason' => 'required|string|max:1000',
        ]);

        $entry->update([
            'status' => 'declined',
            'decline_reason' => $request->decline_reason,
        ]);

        return redirect()->route('workfromhome.show', $id)->with('success', 'Request declined.');
    }

}
