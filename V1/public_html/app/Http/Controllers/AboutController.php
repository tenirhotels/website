<?php

namespace App\Http\Controllers;

use App\Models\Variable;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $variable = Variable::whereSlug('about')->first();

        return view('about', compact('variable'));
    }
}
