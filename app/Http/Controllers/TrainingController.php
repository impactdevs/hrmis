<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrainingRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Training;
use App\Models\User;
use App\Notifications\TrainingApplication;
use App\Notifications\TrainingApproval;
use App\Notifications\TrainingPosted;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Log;

class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::pluck('position_name', 'position_id')->toArray();
        $departments = Department::pluck('department_name', 'department_id')->toArray();
        $users = User::pluck('name', 'id')->toArray() ?? [];

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
        $users_without_all = User::pluck('name', 'id')->toArray();
        $allUsersOption = ['0' => 'All'];
        $users = array_merge($allUsersOption, $users_without_all);
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

            // Process training category to send notifications to them
            $users = array_map('trim', explode(',', $trainingCreated->training_category['users'] ?? ''));

            // If $users has '0' then send notification to all users
            if (in_array('0', $users)) {
                $users = User::all(); // Get all User instances
            } else {
                // Departments
                $departments = array_map('trim', explode(',', $trainingCreated->training_category['departments'] ?? ''));
                Log::info($departments);
                // Get users that belong to these departments by getting user_id where department_id is in $departments from employees table
                $department_users = Employee::whereIn('department_id', $departments)->pluck('user_id')->toArray();
                Log::info($department_users);

                // Positions
                $positions = array_map('trim', explode(',', $trainingCreated->training_category['positions'] ?? ''));
                Log::info('positions', $positions);
                // Get users that belong to these positions by getting user_id where position_id is in $positions from employees table
                $position_users = Employee::whereIn('position_id', $positions)->pluck('user_id')->toArray();
                Log::info($position_users);

                // Combine the two arrays and get unique user IDs
                $userIds = array_unique(array_merge($department_users, $position_users, $users));

                // Fetch User instances based on the unique user IDs
                $users = User::whereIn('id', $userIds)->get();
            }

            //if request has user_id, send a notification to HR
            if (!$request->has('user_id')) {

                Notification::send($users, new TrainingPosted($trainingCreated));
            } else {

            }

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
        $positions = Position::pluck('position_name', 'position_id')->toArray();
        $departments = Department::pluck('department_name', 'department_id')->toArray();
        $users = User::pluck('name', 'id')->toArray() ?? [];

        // Keep the options separate for later use if needed
        $options = [
            'positions' => $positions,
            'departments' => $departments,
            'users' => $users,
        ];

        return view('trainings.show', compact('training', 'options'));
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

    public function apply()
    {
        $user_id = auth()->user()->id;
        return view('trainings.apply', compact('user_id'));
    }

    public function applyTraining(Request $request)
    {
        // Create new training entry
        $training = new Training();
        $training->training_start_date = $request->input('training_start_date');
        $training->training_end_date = $request->input('training_end_date');
        $training->training_location = $request->input('training_location');
        $training->training_title = $request->input('training_title');
        $training->training_description = $request->input('training_description');
        $training->user_id = $request->input('user_id');
        $training->save();

        // Get users with 'super-admin' role
        $users = User::role('HR')->get();

        // Send notification to HR users
        Notification::send($users, new TrainingApplication($training));

        // Redirect back with success message
        return redirect()->route('trainings.index')->with('success', 'Training created successfully.');
    }

    public function approveOrReject(Request $request, Training $training)
    {
        $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();

        // Update leave request based on the user's role and the input status
        if ($user->hasRole('HR')) {
            if ($request->input('status') === 'approved') {
                $training->approval_status = 'HR';
                $training->rejection_reason = null; // Clear reason if approved
            } else {
                $training->approval_status = 'rejected';
                $training->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } elseif ($user->hasRole('Head of Division')) {
            if ($request->input('status') === 'approved') {
                $training->approval_status = 'Head of Division';
                $training->rejection_reason = null; // Clear reason if approved
            } else {
                $training->approval_status = 'rejected';
                $training->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } elseif ($user->hasRole('Executive Secretary')) {
            if ($request->input('status') === 'approved') {
                $training->approval_status = 'approved';
                $training->rejection_reason = null; // Clear reason if approved
            } else {
                $training->approval_status = 'rejected';
                $training->rejection_reason = $request->input('reason'); // Store rejection reason
            }

            // Send notification
            $leaveRequester = User::find($training->user_id); // Get the user who requested the leave
            $approver = User::find(auth()->user()->id);
            Notification::send($leaveRequester, new TrainingApproval($training, $approver));
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }



        // Save the updated leave status
        $training->save();

        return response()->json(['message' => 'Training application updated successfully.', 'status' => $training->approval_status]);
    }


}
