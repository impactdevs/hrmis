<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\WhistleblowingReport;
use Illuminate\Support\Str;



class WhistleblowingController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\WhistleblowingReport::query();

        // Filter by submission type
        if ($request->filled('type')) {
            $query->where('submission_type', $request->input('type'));
        }
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $reports = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('whistleblowing.index', compact('reports'));
    }

    public function show($id)
    {
        $report = \App\Models\WhistleblowingReport::findOrFail($id);
        return view('whistleblowing.show', compact('report'));
    }
    // Show the whistleblowing form
    public function create()
    {
        return view('whistleblowing.create'); // Assuming your blade file is named 'form.blade.php'
    }

    // Process the form submission
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'submission_type' => 'required|string|max:255',
            'submission_type_other' => 'nullable|string|max:255',
            'description' => 'required|string|min:20|max:5000',
            'individuals_involved' => 'required|string|min:10|max:2000',
            'evidence_details' => 'required|string|min:10|max:2000',
            'evidence_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
            'reported_before' => 'required|in:Yes,No,I don\'t know',
            'reported_details' => 'nullable|string|max:2000',
            'suggested_resolution' => 'nullable|string|max:2000',
            'confirmation' => 'required|accepted'
        ]);

        // Handle file upload using public storage
        $filePath = null;
        if ($request->hasFile('evidence_file')) {
            $filePath = $request->file('evidence_file')->store('whistleblower_evidence', 'public');
        }

        $tracking_id = 'WB-'.date('Ymd') .'-'.strtoupper(Str::random(6));

        // Create report
        $report = WhistleblowingReport::create([
            'submission_type' => $validated['submission_type'] === 'Other'
                ? $validated['submission_type_other']
                : $validated['submission_type'],
            'description' => $validated['description'],
            'individuals_involved' => $validated['individuals_involved'],
            'evidence_details' => $validated['evidence_details'],
            'evidence_file_path' => $filePath,
            'reported_before' => $validated['reported_before'],
            'reported_details' => $validated['reported_details'] ?? null,
            'suggested_resolution' => $validated['suggested_resolution'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'tracking_id' => $tracking_id
        ]);

        return to_route('whistle.thankyou')->with([
            'success' => 'Application submitted successfully!',
            'report_id' => $tracking_id
        ]);
    }

    public function thankyou()
    {
        return view('whistleblowing.received');
    }
}
