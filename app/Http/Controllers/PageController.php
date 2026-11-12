<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Page;
use App\Support\Seo;

class PageController extends Controller
{
    public function show(string $page)
    {
        $page = Page::published()->where('slug', $page)->firstOrFail();

        return view('site.page', [
            'page' => $page,
            'seo' => Seo::for($page),
        ]);
    }

    public function contact()
    {
        return view('site.contact', [
            'hotels' => Hotel::published()->open()->ordered()->get(),
            'seo' => Seo::make(__('site.nav.contact')),
        ]);
    }
}
