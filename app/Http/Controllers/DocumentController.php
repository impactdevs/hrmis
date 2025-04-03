<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function uncst_matrix()
    {
        return view('documents.uncst-matrix');
    }
}
