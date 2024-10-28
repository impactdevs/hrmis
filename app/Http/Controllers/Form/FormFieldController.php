<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use App\Models\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $field = new FormField();
        $field->section_id = $request->input('section_id');
        $field->label = $request->input('label');
        $field->type = $request->input('type');
        $field->options = $request->input('options');
        $field->repeater_options = $request->input('repeater_options');
        $field->save();

        if ($field->type == 'table') {
            //iterate through, 
            $save_table_rows = DB::table('table_field_rows')->create(
                [

                ]

            );
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(['success' => true, 'data' => FormField::find($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // use update method except _token and _method
        $form_field = FormField::find($id);

        //update the label, options and type in case they are changed
        $form_field->label = $request->input('label');
        $form_field->options = $request->input('options');
        $form_field->type = $request->input('type');
        $form_field->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $field = FormField::find($id);
        $form_id = $field->form_id;
        $field->delete();

        return redirect()->back();
    }

    // add conditional visibility field
    public function addConditionalVisibilityField(Request $request)
    {
        //save condition
        $save = DB::table('field_properties')->insert([
            'field_id' => $request->input('field_id'),
            'conditional_visibility_field_id' => $request->input('conditional_visibility_field_id'),
            'conditional_visibility_operator' => $request->input('conditional_visibility_operator'),
        ]);

        return redirect()->back();
    }

    //retrieve conditional visibility field
    public function getConditionalVisibilityField($field_id)
    {
        $conditional_visibility_field = DB::table('field_properties')
            ->join('form_fields', 'field_properties.conditional_visibility_field_id', '=', 'form_fields.id')
            ->select('field_properties.*', 'form_fields.label')
            ->where('field_properties.field_id', $field_id)
            ->get();

        return response()->json(['success' => true, 'data' => $conditional_visibility_field]);
    }

}
