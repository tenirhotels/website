<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Общие поля контента: SEO, публикация, сортировка, адрес по слагу.
 * Колонки: slug, seo_title (json), seo_description (json), og_image, is_published, sort.
 */
trait HasSeo
{
    public function initializeHasSeo(): void
    {
        $this->mergeCasts([
            'seo_title' => 'array',
            'seo_description' => 'array',
            'seo_keywords' => 'array',
            'is_published' => 'boolean',
        ]);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where($this->getTable().'.is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy($this->getTable().'.sort')->orderBy($this->getTable().'.id');
    }

    public function seoTitle(): ?string
    {
        return $this->t('seo_title') ?: $this->t($this->titleAttribute ?? 'title');
    }

    public function seoKeywords(): ?string
    {
        return $this->t('seo_keywords');
    }

    public function seoDescription(): ?string
    {
        return $this->t('seo_description') ?: $this->t('summary');
    }
}
