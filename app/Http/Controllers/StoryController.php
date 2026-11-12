<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Support\Seo;

class StoryController extends Controller
{
    public function index()
    {
        return view('site.stories.index', [
            'stories' => Story::live()->latest('published_at')->paginate(12),
            'seo' => Seo::make(__('site.nav.stories')),
        ]);
    }

    public function show(Story $story)
    {
        abort_unless($story->is_published && $story->published_at?->isPast(), 404);

        return view('site.stories.show', [
            'story' => $story,
            'more' => Story::live()->whereKeyNot($story->id)->latest('published_at')->limit(3)->get(),
            'seo' => Seo::for($story, $story->hotel?->t('name'))->ofType('article')->withSchema([
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $story->t('title'),
                'datePublished' => $story->published_at?->toAtomString(),
            ]),
        ]);
    }
}
