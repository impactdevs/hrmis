<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PublicHoliday;

class PublicHolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $holidays = PublicHoliday::all();
        return view('public_holidays.index', compact('holidays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('public_holidays.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'holiday_name' => 'required|string|max:255',
            'holiday_date' => 'required',
        ]);

        PublicHoliday::create($request->all());

        return redirect()->route('public_holidays.index')->with('success', 'Public holiday created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $holiday = PublicHoliday::findOrFail($id);
        return view('public_holidays.show', compact('holiday'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $holiday = PublicHoliday::findOrFail($id);
        return view('public_holidays.edit', compact('holiday'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'holiday_name' => 'required|string|max:255',
            'holiday_date' => 'required|date',
        ]);

        $holiday = PublicHoliday::findOrFail($id);
        $holiday->update($request->all());

        return redirect()->route('public_holidays.index')->with('success', 'Public holiday updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $holiday = PublicHoliday::findOrFail($id);
        $holiday->delete();

        return redirect()->route('public_holidays.index')->with('success', 'Public holiday deleted successfully.');
    }
}
