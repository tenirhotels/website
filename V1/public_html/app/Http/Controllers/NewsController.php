<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Variable;

class NewsController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('published_at', 'DESC')->paginate(4);
        $setting  = Variable::whereSlug('settings')->first();

        return view('news.index', compact('posts', 'setting'));
    }

    public function show(Post $post)
    {
        return view('news.show', compact('post'));
    }
}
