<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Add this if not already imported


class FormController extends Controller
{
    public function index()
    {
        $forms = Form::with('sections.fields', 'setting')->get();
        return view('forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('forms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            // Add validation rules for other fields as necessary
        ]);

        // Create a new Form record
        $form = Form::create([
            'uuid' => Str::uuid(), // Generate UUID
            'name' => $request->input('name'),

        ]);

        // Redirect to the show route for the newly created form
        return redirect()->route('form-builder.show', $form->uuid);
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        // Check where uuid is equal to the form id
        $form = Form::where('uuid', $uuid)->first();

        // Load sections with their related fields and field properties
        $form->load(['sections.fields.properties']);

        // dd($form->sections[0]->fields[2]->properties[0]->conditional_visibility_field_id);
        return view('forms.show', compact('form'));
    }

    /**
     * Display the specified resource.
     */
    public function display_questionnaire($uuid)
    {
        // Check where uuid is equal to the form id
        $form = Form::where('uuid', $uuid)->first();

        // Load sections with their related fields
        $form->load(['sections.fields']);
        return view('entries.create', compact('form'));
    }
}
