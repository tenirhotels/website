<?php

namespace App\Http\Controllers;

use App\Models\Post;

class BookingController extends Controller
{
    public function index()
    {

        return view('booking', compact('posts'));
    }
}
