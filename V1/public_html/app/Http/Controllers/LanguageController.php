<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function update($lang)
    {

        session(['lang' => $lang]);

        return redirect()->back();
    }
}
