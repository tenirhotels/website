<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Support\Seo;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $categories = config('tenir.experience_categories');

        $experiences = Experience::published()->with('hotel')
            ->when(isset($categories[$category]), fn ($q) => $q->where('category', $category))
            ->ordered()->get();

        return view('site.experiences.index', [
            'experiences' => $experiences,
            'categories' => Experience::published()->distinct()->pluck('category')
                ->filter(fn ($c) => isset($categories[$c]))->values(),
            'active' => isset($categories[$category]) ? $category : null,
            'seo' => Seo::make(__('site.nav.experiences'), __('site.experiences.intro')),
        ]);
    }

    public function show(Experience $experience)
    {
        abort_unless($experience->is_published, 404);

        return view('site.experiences.show', [
            'experience' => $experience,
            'related' => Experience::published()->whereKeyNot($experience->id)
                ->where('category', $experience->category)->ordered()->limit(3)->get(),
            'seo' => Seo::for($experience),
        ]);
    }
}
