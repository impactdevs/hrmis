<?php

namespace App\Http\Controllers;

use App\Http\Requests\OutStationTrainingRequest;
use App\Models\Department;
use App\Models\OutOfStationTraining;
use App\Models\Position;
use App\Models\User;
use App\Notifications\OutOfStationApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class OutOfStationTrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::pluck('name', 'id')->toArray() ?? [];

        // Keep the options separate for later use if needed
        $options = [
            'users' => $users,
        ];

        $trainings = OutOfStationTraining::paginate();

        return view('out-of-station-training.index', compact('trainings', 'options'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::whereNotIn('id', [auth()->user()->id])->pluck('name', 'id')->toArray();
        return view('out-of-station-training.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OutStationTrainingRequest $request)
    {
        // Get the request data as an array
        $data = $request->all();
        // Format (qualification details documents
        if (filled($data['relevant_documents'])) {
            foreach ($data['relevant_documents'] as $key => $value) {
                // Check if a file is uploaded for this qualification
                // Use the correct input name to check for the file
                if ($request->hasFile("relevant_documents.$key.proof")) {
                    // Store the file and get the path
                    $filePath = $request->file("relevant_documents.$key.proof")->store('relevant_documents', 'public');

                    // Update the proof value to the path
                    $data['relevant_documents'][$key]['proof'] = $filePath;

                }
            }
        }

        $data['user_id'] = auth()->user()->id;
        $training = OutOfStationTraining::create($data);

        return redirect()->route('out-of-station-trainings.index')->with('success', 'Travel Clearance created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OutOfStationTraining $out_of_station_training)
    {
        $users = User::pluck('name', 'id')->toArray() ?? [];

        // Keep the options separate for later use if needed
        $options = [
            'users' => $users,
        ];

        $training = $out_of_station_training;
        return view('out-of-station-training.show', compact('training', 'options'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OutOfStationTraining $out_of_station_training)
    {
        $training = $out_of_station_training;
        $users = User::pluck('name', 'id')->toArray() ?? [];


        return view('out-of-station-training.edit', compact('training', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OutOfStationTraining $outOfStationTraining)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OutOfStationTraining $outOfStationTraining)
    {
        //
    }

    public function approveOrReject(Request $request, OutOfStationTraining $training)
    {

        $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();

        // Retrieve current training_request_status (it will be an array due to casting)
        $trainingRequestStatus = $training->training_request_status ?: []; // Default to an empty array if null

        // Update leave request based on the user's role and the input status
        if ($user->hasRole('HR')) {
            if ($request->input('status') === 'approved') {
                // Set HR status to approved
                $trainingRequestStatus['HR'] = 'approved';
                $training->rejection_reason = null; // Clear reason if approved
            } else {
                // Set HR status to rejected
                $trainingRequestStatus['HR'] = 'rejected';
                $training->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } elseif ($user->hasRole('Head of Division')) {
            if ($request->input('status') === 'approved') {
                // Set Head of Division status to approved
                $trainingRequestStatus['Head of Division'] = 'approved';
                $training->rejection_reason = null; // Clear reason if approved
            } else {
                // Set Head of Division status to rejected
                $trainingRequestStatus['Head of Division'] = 'rejected';
                $training->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } elseif ($user->hasRole('Executive Secretary')) {
            if ($request->input('status') === 'approved') {
                // Set leave status as approved for Executive Secretary
                $trainingRequestStatus['Executive Secretary'] = 'approved';
                $training->rejection_reason = null; // Clear reason if approved
            } else {
                // Set rejection status
                $trainingRequestStatus['Executive Secretary'] = 'rejected';
                $training->rejection_reason = $request->input('reason'); // Store rejection reason
            }

            // Send notification
            $trainingRequester = User::find($training->user_id); // Get the user who requested the leave
            $approver = User::find(auth()->user()->id);
            Notification::send($trainingRequester, new OutOfStationApproval($training, $approver));
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Save the updated training_request_status
        $training->training_request_status = $trainingRequestStatus;
        $training->save();

        return response()->json(['message' => 'Leave application updated successfully.', 'status' => $training->training_request_status]);
    }
}
