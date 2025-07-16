<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Whistleblower;
use App\Models\Evidence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Notifications\WhistleblowerReported;
use Exception;

class WhistleblowerController extends Controller
{
    /**
     * Display a listing of the whistleblower reports.
     */
    public function index(): View
    {
        $whistleblowers = Whistleblower::with('evidence')->latest()->paginate(10);
        return view('whistleblower.index', compact('whistleblowers'));
    }

    /**
     * Show the form to submit a new whistleblower report.
     */
    public function create(): View
    {
        return view('whistleblower.create');
    }

    /**
     * Store the submitted whistleblower report and related evidence(s).
     */
  public function store(Request $request)
{
    try {
        $validated = $request->validate([
            // Whistleblower fields
            'employee_name' => 'nullable|string|max:255',
            'employee_email' => 'nullable|email|max:255',
            'employee_department' => 'nullable|string|max:255',
            'employee_telephone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'submission_type' => 'required|string',
            'description' => 'required|string',
            'individuals_involved' => 'nullable|string',
            'issue_reported' => 'required|string',
            'resolution' => 'nullable|string',
            'confidentiality_statement' => 'required|string',

            // Evidence fields (array-based)
            'witness_name.*' => 'nullable|string|max:255',
            'email.*' => 'nullable|email|max:255',
            'document.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xlsx|max:2048',
        ]);

        DB::beginTransaction();

        // ✅ Insert into whistleblowers table
        $whistleblower = Whistleblower::create([
            'employee_name' => $validated['employee_name'],
            'employee_email' => $validated['employee_email'],
            'employee_department' => $validated['employee_department'],
            'employee_telephone' => $validated['employee_telephone'],
            'job_title' => $validated['job_title'],
            'submission_type' => $validated['submission_type'],
            'description' => $validated['description'],
            'individuals_involved' => $validated['individuals_involved'],
            'issue_reported' => $validated['issue_reported'],
            'resolution' => $validated['resolution'] ?? 'not applicable',
            'confidentiality_statement' => $validated['confidentiality_statement'],
        ]);

        // ✅ Insert each evidence (linked by whistleblower_id)
        if ($request->has('witness_name')) {
        foreach ($request->witness_name as $index => $name) {
        if (
            empty($name) &&
            empty($request->email[$index]) &&
            !$request->hasFile("document.$index")
            ) {
            continue;
            }

            $documentPath = null;
            if ($request->hasFile("document.$index")) {
            $documentPath = $request->file("document.$index")->store('evidence_files', 'public');
            }

                Evidence::create([
                    'witness_name'     => $name,
                    'email'            => $request->email[$index],
                    'document'         => $documentPath,
                    'whistleblower_id' => $whistleblower->whistleblower_id,
                 ]);
            }
        }


        DB::commit();
        return redirect()->route('whistleblower.index')->with('success', 'Whistleblower report submitted successfully.');
    } catch (Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Error submitting report: ' . $e->getMessage());
    }
}

    /**
     * Display a single whistleblower report with evidence.
     */
    public function show(Whistleblower $whistleblower): View
    {
        $whistleblower->load('evidence');
        return view('whistleblower.show', compact('whistleblower'));
    }
}
