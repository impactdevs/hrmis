<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use App\Notifications\Welcome;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Contract;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $position_id = $request->get('position');
        $department_id = $request->get('department');
        $contract_expiry_filter = (int) $request->get('contract_expiry');
        $perPage = 25;

        $query = Employee::query();

        // Initialize the applied filters message
        $appliedFiltersMessage = [];

        // Apply the filters to the query
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")
                    ->orWhere('last_name', 'LIKE', "%$keyword%");
            });
            $appliedFiltersMessage[] = "Name: $keyword";
        }

        if (!empty($position_id)) {
            $query->where('position_id', '=', $position_id);
            $appliedFiltersMessage[] = "Position: " . Position::find($position_id)->position_name;
        }

        if (!empty($department_id)) {
            $query->where('department_id', '=', $department_id);
            $appliedFiltersMessage[] = "Department: " . Department::find($department_id)->department_name;
        }

        if (!empty($contract_expiry_filter)) {
            $currentDate = now();
            $filterDate = now()->addMonths($contract_expiry_filter); // Calculate the date based on selected months
            $query->whereDate('contract_expiry_date', '<=', $currentDate);
            $appliedFiltersMessage[] = "Contract expiring in $contract_expiry_filter months";
        }

        // Get the count of the filtered results
        $employeeCount = $query->count();

        // Paginate the results
        $employees = $query->latest()->get();

        // Get positions, departments, and the available contract expiry options
        $positions = Position::select('position_id', 'position_name')->get();
        $departments = Department::select('department_id', 'department_name')->get();
        $expiryOptions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

        // Return the view with the filtered results, employee count, filter options, and filter message
        return view('employees.index', compact('employees', 'keyword', 'positions', 'departments', 'expiryOptions', 'employeeCount', 'appliedFiltersMessage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get departments with department_id as key and department_name as the value
        $departments = Department::pluck('department_name', 'department_id')->toArray();

        //positions
        $positions = Position::pluck('position_name', 'position_id')->toArray();

        return view('employees.create', compact('departments', 'positions'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            // Initialize an array to hold the validated data
            $validatedData = $request->validated();

            // Handle the passport photo upload
            if ($request->hasFile('passport_photo')) {
                // Store the photo and get the path
                $passportPhotoPath = $request->file('passport_photo')->store('passport_photos', 'public');
                // Add the path to the validated data
                $validatedData['passport_photo'] = $passportPhotoPath;
            }

            // Handle the passport photo upload
            if ($request->hasFile('national_id_photo')) {
                // Store the photo and get the path
                $passportPhotoPath = $request->file('national_id_photo')->store('national_id_photos', 'public');
                // Add the path to the validated data
                $validatedData['national_id_photo'] = $passportPhotoPath;
            }

            // Format (qualification details documents
            if (filled($validatedData['qualifications_details'])) {
                foreach ($validatedData['qualifications_details'] as $key => $value) {
                    // Check if a file is uploaded for this qualification
                    // Use the correct input name to check for the file
                    if ($request->hasFile("qualifications_details.$key.proof")) {
                        // Store the file and get the path
                        $filePath = $request->file("qualifications_details.$key.proof")->store('proof_documents', 'public');

                        // Update the proof value to the path
                        $validatedData['qualifications_details'][$key]['proof'] = $filePath;
                    }
                }
            }

            // Format (qualification details documents
            if (filled($validatedData['contract_documents'])) {
                foreach ($validatedData['contract_documents'] as $key => $value) {
                    // Check if a file is uploaded for this qualification
                    // Use the correct input name to check for the file
                    if ($request->hasFile("contract_documents.$key.proof")) {
                        // Store the file and get the path
                        $filePath = $request->file("contract_documents.$key.proof")->store('contract_documents', 'public');

                        // Update the proof value to the path
                        $validatedData['contract_documents'][$key]['proof'] = $filePath;
                    }
                }
            }
            $user = DB::table('users')->where('email', $validatedData['email'])->doesntExist();
            if ($user) {
                $password = Str::random(10);
                Log::info('Password generated for ' . $validatedData['email'] . ': ' . $password);
                $user = new User();
                $user->email = $validatedData['email'];
                $user->name = $validatedData['first_name'] . ' ' . $validatedData['last_name'];
                $user->password = Hash::make($password);
                $user->save();
                $user->assignRole('Staff'); // Ensure the role exists
                $validatedData['user_id'] = $user->id;
                // Send a welcome notification with credentials
                $user->notify(new Welcome($user->email, $password));
            } else {
                //add user_id to employee
                $validatedData['user_id'] = User::where('email', $validatedData['email'])->first()->id;
            }
            // Create a new employee record using validated data
            Employee::create($validatedData);

            // Redirect to the employees index with a success message
            return redirect()->route('employees.index')->with('success', 'Employee Registered');
        } catch (Exception $exception) {
            // Log the error for debugging
            Log::error('Error adding employee: ' . $exception);

            // Redirect back with an error message
            return redirect()->back()->with('error', 'Problem Adding the Employee');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        // Eager load relationships
        $employee->load('department', 'position', 'contracts'); // Add any other relationships you need

        return view('employees.show', compact('employee'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        // Get departments with department_id as key and department_name as the value
        $departments = Department::pluck('department_name', 'department_id')->toArray();

        //positions
        $positions = Position::pluck('position_name', 'position_id')->toArray();

        return view('employees.edit', compact('employee', 'departments', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreEmployeeRequest $request, Employee $employee)
    {
        try {
            //record being updated
            $qualification_details = $employee->qualifications_details;
            // Initialize an array to hold the validated data
            $validatedData = $request->validated();

            // Handle the passport photo upload
            if ($request->hasFile('passport_photo')) {
                // Store the photo and get the path
                $passportPhotoPath = $request->file('passport_photo')->store('passport_photos', 'public');
                // Add the path to the validated data
                $validatedData['passport_photo'] = $passportPhotoPath;
            }

            // Handle the passport photo upload
            if ($request->hasFile('national_id_photo')) {
                // Store the photo and get the path
                $passportPhotoPath = $request->file('national_id_photo')->store('national_id_photos', 'public');
                // Add the path to the validated data
                $validatedData['national_id_photo'] = $passportPhotoPath;
            }

            // Format (qualification details documents
            if (filled($validatedData['qualifications_details'])) {
                foreach ($validatedData['qualifications_details'] as $key => $value) {
                    // Check if the current qualification has a proof file
                    if ($request->hasFile("qualifications_details.$key.proof")) {
                        // Store the file and get the path
                        $filePath = $request->file("qualifications_details.$key.proof")->store('proof_documents', 'public');

                        // Update the proof value to the path
                        $qualification_details[$key]['proof'] = $filePath;
                    }

                    //check if there is title
                    if (isset($validatedData['qualifications_details'][$key]['title'])) {
                        $qualification_details[$key]['title'] = $validatedData['qualifications_details'][$key]['title'];
                    }

                    // Update the qualification details
                    $validatedData['qualifications_details'] = $qualification_details;
                }
            }


            // Format (qualification details documents
            if (filled($validatedData['contract_documents'])) {
                foreach ($validatedData['contract_documents'] as $key => $value) {
                    // Check if the current qualification has a proof file
                    if ($request->hasFile("contract_documents.$key.proof")) {
                        // Store the file and get the path
                        $filePath = $request->file("contract_documents.$key.proof")->store('contract_documents', 'public');

                        // Update the proof value to the path
                        $qualification_details[$key]['proof'] = $filePath;
                    }

                    //check if there is title
                    if (isset($validatedData['contract_documents'][$key]['title'])) {
                        $qualification_details[$key]['title'] = $validatedData['contract_documents'][$key]['title'];
                    }

                    // Update the qualification details
                    $validatedData['contract_documents'] = $qualification_details;
                }
            }

            // Update the employee record using validated data
            $employee->update($validatedData);

            // Redirect to the employees index with a success message
            return redirect()->route('employees.index')->with('success', 'Employee Updated');
        } catch (Exception $exception) {
            // Log the error for debugging
            Log::error('Error updating employee: ' . $exception->getMessage());

            // Redirect back with an error message
            return redirect()->back()->with('error', 'Problem Updating the Employee');
        }
    }

    // In EmployeeController.php
    public function updateEntitledLeaveDays(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        // Validate the input
        $validated = $request->validate([
            'entitled_leave_days' => 'required|numeric|min:0',
        ]);

        // Update the employee's entitled leave days
        $employee->entitled_leave_days = $request->input('entitled_leave_days');
        $employee->save();

        return response()->json(['success' => true]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            // Delete the employee record
            $employee->delete();

            // Redirect to the employees index with a success message
            return redirect()->route('employees.index')->with('success', 'Employee Deleted');
        } catch (Exception $exception) {
            // Log the error for debugging
            Log::error('Error deleting employee: ' . $exception->getMessage());

            // Redirect back with an error message
            return redirect()->back()->with('error', 'Problem Deleting the Employee');
        }
    }

    public function generatePDF(Employee $employee)
    {
        $data = ['employee' => $employee];
        $pdf = PDF::loadView('employees.pdf', $data);
        return $pdf->stream('employee-profile.pdf');
    }

    public function create_contract($employee_id)
    {
        $employee = Employee::findOrFail($employee_id);
        $users = User::all();
        return view('employees.create_contract', compact('employee', 'users'));
    }

    public function store_contract(Request $request)
    {
        //get the data for processing
        $requestedData = $request->all();

        //check if the request contains contract_documents
        if (filled($requestedData['contract_documents'])) {
            foreach ($requestedData['contract_documents'] as $key => $value) {
                // Check if a file is uploaded for this qualification
                // Use the correct input name to check for the file
                if ($request->hasFile("contract_documents.$key.proof")) {
                    // Store the file and get the path
                    $filePath = $request->file("contract_documents.$key.proof")->store('contract_documents', 'public');

                    // Update the proof value to the path
                    $requestedData['contract_documents'][$key]['proof'] = $filePath;
                }
            }
        }

        Contract::create($requestedData);


        // Redirect to the employees index with a success message
        return to_route('employees.show', ['employee' => $requestedData['employee_id']]);
    }

    public function update_contract(Request $request, Contract $contract)
    {
        $requestedData = $request->all();
        // Format (qualification details documents
        if (filled($requestedData['contract_documents'])) {
            $qualification_details = $contract->contract_documents;
            foreach ($requestedData['contract_documents'] as $key => $value) {

                // Check if the current qualification has a proof file
                if ($request->hasFile("contract_documents.$key.proof")) {
                    // Store the file and get the path
                    $filePath = $request->file("contract_documents.$key.proof")->store('contract_documents', 'public');

                    // Update the proof value to the path
                    $qualification_details[$key]['proof'] = $filePath;
                }

                //check if there is title
                if (isset($requestedData['contract_documents'][$key]['title'])) {
                    $qualification_details[$key]['title'] = $requestedData['contract_documents'][$key]['title'];
                }

                // Update the qualification details
                $requestedData['contract_documents'] = $qualification_details;
            }
        }
        $contract->update($requestedData);

        return to_route('employees.show', ['employee' => $contract->employee_id]);
    }

    public function edit_contract(Request $request, Contract $contract)
    {
        $contract->load('employee');
        $users = User::all();
        return view('employees.edit_contract', compact('contract', 'users'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy_contract(Contract $contract)
    {
        try {
            // Delete the employee record
            $contract->delete();

            // Redirect to the employees index with a success message
            return to_route('employees.show', ['employee' => $contract->employee_id]);
        } catch (Exception $exception) {
            // Log the error for debugging
            Log::error('Error deleting employee: ' . $exception->getMessage());

            // Redirect back with an error message
            return redirect()->back()->with('error', 'Problem Deleting the Employee');
        }
    }
}
