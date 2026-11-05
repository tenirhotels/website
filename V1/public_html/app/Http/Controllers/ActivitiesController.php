<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Variable;
use Illuminate\Http\Request;

class ActivitiesController extends Controller
{
    public function index()
    {
        $activities = Activity::orderBy('position')->get();
        $setting  = Variable::whereSlug('settings')->first();

        return view('activities', compact('activities', 'setting'));
    }
}
