<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrainingRequest;
use App\Models\Department;
use App\Models\Position;
use App\Models\Training;
use App\Models\User;
use App\Notifications\TrainingPosted;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::pluck('position_name', 'position_id')->toArray();
        $departments = Department::pluck('department_name', 'department_id')->toArray();
        $users = User::pluck('name', 'id')->toArray()??[];

        // Keep the options separate for later use if needed
        $options = [
            'positions' => $positions,
            'departments' => $departments,
            'users' => $users,
        ];

        $trainings = Training::paginate(10);

        return view('trainings.index', compact('trainings', 'options'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $positions = Position::pluck('position_name', 'position_id')->toArray();
        $departments = Department::pluck('department_name', 'department_id')->toArray();
        $users = User::pluck('name', 'id')->toArray();
        $options = array_merge($positions, $departments, $users);

        return view('trainings.create', compact('options', 'users', 'positions', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrainingRequest $request)
    {
        try {
            // Initialize an array to hold the validated data
            $validatedData = $request->validated();

            // Create the training
            $trainingCreated = Training::create($validatedData);

            $users = User::role('Super Admin')->get();

            Notification::send($users, new TrainingPosted($trainingCreated));

            return redirect()->route('trainings.index')->with('success', 'Training created successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Error creating training: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Training $training)
    {
        return view('trainings.show', compact('training'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Training $training)
    {
        $positions = Position::pluck('position_name', 'position_id')->toArray();
        $departments = Department::pluck('department_name', 'department_id')->toArray();
        $users = User::pluck('name', 'id')->toArray();
        $options = array_merge($positions, $departments, $users);

        $selectedOptions = $training->training_category;

        return view('trainings.edit', compact('options', 'training', 'users', 'departments', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TrainingRequest $request, Training $training)
    {
        try {
            // Initialize an array to hold the validated data
            $validatedData = $request->validated();

            // Update the training
            $training->update($validatedData);

            return redirect()->route('trainings.index')->with('success', 'Training updated successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Error updating training: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Training $training)
    {
        try {
            $training->delete();
            return redirect()->route('trainings.index')->with('success', 'Training deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Error deleting training: ' . $e->getMessage());
        }
    }
}
