<?php

namespace App\Http\Controllers;

use App\Models\WorkFromHome;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Notifications\WorkFromHomeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;


class WorkFromHomeController extends Controller
{
    public function index()
    {
        $entries = WorkFromHome::with('employee', 'tasks')->paginate(10);
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
            'work_from_home_attachments' => 'nullable|mimes:pdf,jpg,jpeg,png,doc,docx,xlsx|max:10240',

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

        try {
            // Create work from home record
            $workFromHome = WorkFromHome::create([
                ...$validated,
                'employee_id' => $employeeId,
            ]);

            // Save each task
            $startDates = $request->input('task_start_date', []);
            $endDates = $request->input('task_end_date', []);
            $descriptions = $request->input('description', []);

            foreach ($descriptions as $index => $desc) {
                Task::create([
                    'task_id'            => Str::uuid(),
                    'work_from_home_id'  => $workFromHome->work_from_home_id,
                    'task_start_date'    => $startDates[$index],
                    'task_end_date'      => $endDates[$index] ?? null,
                    'description'        => $desc,
                ]);
            }

            $hrUser = User::role('HR')->first();
            if ($hrUser) {
                Notification::send($hrUser, new WorkFromHomeNotification($workFromHome, auth()->user()->employee->first_name, auth()->user()->employee->last_name));
            }



            return redirect()->route('workfromhome.index')->with('success', 'Work from home request created successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to save request: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $entry = WorkFromHome::with('employee', 'tasks')->findOrFail($id);
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
}
