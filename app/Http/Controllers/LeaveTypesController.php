<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaveTypes = LeaveType::all();
        return view('leave-types.index', compact('leaveTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('leave-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        LeaveType::create($request->all());

        return redirect()->route('leave-types.index')->with('success', 'Event Type created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveType $leaveType)
    {
        return view('leave-types.show', compact('leaveType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveType $leaveType)
    {
        return view('leave-types.edit', compact('leaveType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeaveType $leaveType)
    {
        //update
        $leaveType->update($request->all());

        return redirect()->route('leave-types.index')->with('success', 'Leave Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();

        return redirect()->route('leave-types.index')->with('success', 'Leave Type deleted successfully.');
    }
}
