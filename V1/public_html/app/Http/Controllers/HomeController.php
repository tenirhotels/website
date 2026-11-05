<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Variable;

class HomeController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('published_at', 'DESC')->limit(6)->get();
        $variable = Variable::whereSlug('home')->first();
        $settings =  Variable::whereSlug('settings')->firstOrFail();

        return view('index', compact('posts', 'variable', 'settings'));
    }
}
