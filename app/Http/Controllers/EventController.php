<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Department;
use App\Models\Event;
use App\Models\Position;
use App\Models\User;
use App\Notifications\EventPosted;
use Exception;
use Illuminate\Support\Facades\Notification;
use Log;
use App\Models\Employee;


class EventController extends Controller
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

        $events = Event::paginate(10);
        return view('events.index', compact('events', 'options'));
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
        //sort users to start with 'All'
        $options = array_merge($positions, $departments, $users);

        return view('events.create', compact('options', 'users', 'positions', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request)
    {
        try {
            // Initialize an array to hold the validated data
            $validatedData = $request->validated();

            // Create the event
            $eventCreated = Event::create($validatedData);

            // Process training category to send notifications to them
            $users = array_map('trim', explode(',', $eventCreated->category['users'] ?? ''));

            // If $users has '0' then send notification to all users
            if (in_array('0', $users)) {
                $users = User::all(); // Get all User instances
            } else {
                $departments = array_map('trim', explode(',', $eventCreated->category['departments'] ?? ''));

                // Get users that belong to these departments by getting user_id where department_id is in $departments from employees table
                $department_users = Employee::whereIn('department_id', $departments)->pluck('user_id')->toArray();

                // Positions
                $positions = array_map('trim', explode(',', $eventCreated->category['positions'] ?? ''));
                // Get users that belong to these positions by getting user_id where position_id is in $positions from employees table
                $position_users = Employee::whereIn('position_id', $positions)->pluck('user_id')->toArray();

                // Combine the two arrays and get unique user IDs
                $userIds = array_unique(array_merge($department_users, $position_users, $users));

                // Fetch User instances based on the unique user IDs
                $users = User::whereIn('id', $userIds)->get();

            }


            Notification::send($users, new EventPosted($eventCreated));

            return redirect()->route('events.index')->with('success', 'Event created successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Error creating event: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
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

        $events = Event::paginate(10);
        return view('events.show', compact('event', 'options'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $positions = Position::pluck('position_name', 'position_id')->toArray();
        $departments = Department::pluck('department_name', 'department_id')->toArray();
        $users_without_all = User::pluck('name', 'id')->toArray();
        $allUsersOption = ['0' => 'All'];
        $users = array_merge($allUsersOption, $users_without_all);
        //sort users to start with 'All'
        $options = array_merge($positions, $departments, $users);
        return view('events.edit', compact('options', 'users', 'positions', 'departments', 'event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, Event $event)
    {
        try {
            // Initialize an array to hold the validated data
            $validatedData = $request->validated();

            // Update the event
            $event->update($validatedData);

            return redirect()->route('events.index')->with('success', 'Event updated successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Error updating event: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        try {
            $event->delete();
            return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Error deleting event: ' . $e->getMessage());
        }
    }
}
