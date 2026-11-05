<?php

namespace App\Support;

use App\Models\Hotel;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/**
 * SEO-данные страницы: title, description, картинка, canonical, hreflang, schema.org.
 */
class Seo
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public array $schema = [],
        public bool $noindex = false,
    ) {}

    public static function make(?string $title = null, ?string $description = null, ?string $image = null): self
    {
        return new self($title, $description, $image);
    }

    /** Для записи с HasSeo (Hotel, Offer, ...). */
    public static function for($model, ?string $suffix = null): self
    {
        $title = $model->seoTitle();

        return new self(
            title: $suffix ? "$title — $suffix" : $title,
            description: $model->seoDescription(),
            image: $model->og_image ?: ($model->hero_image ?? $model->image ?? null),
        );
    }

    public function withSchema(array $schema): self
    {
        $this->schema[] = $schema;

        return $this;
    }

    public function noindex(): self
    {
        $this->noindex = true;

        return $this;
    }

    public function fullTitle(): string
    {
        $brand = Setting::text('brand.name', 'Tenir Hotels');
        $title = $this->title ?: Setting::text('seo.default_title', $brand);

        return str_contains($title, $brand) ? $title : "$title | $brand";
    }

    public function metaDescription(): ?string
    {
        $text = $this->description ?: Setting::text('seo.default_description');

        return $text ? str(strip_tags($text))->squish()->limit(165)->toString() : null;
    }

    public function imageUrl(): ?string
    {
        $path = $this->image ?: Setting::get('seo.og_image');

        return $path ? (str_starts_with($path, 'http') ? $path : url(Storage::disk('public')->url($path))) : null;
    }

    /** Адрес текущей страницы на каждом языке. @return array<string, string> */
    public static function alternates(): array
    {
        $route = Route::current();
        if (! $route || ! $route->getName()) {
            return [];
        }

        $urls = [];
        foreach (Locales::codes() as $code) {
            $urls[$code] = route($route->getName(), [...$route->parameters(), 'locale' => $code]);
        }

        return $urls;
    }

    public static function canonical(): string
    {
        return url()->current();
    }

    /** schema.org/Hotel */
    public static function hotelSchema(Hotel $hotel): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Hotel',
            'name' => $hotel->t('name'),
            'description' => strip_tags((string) $hotel->t('summary')),
            'url' => $hotel->url(),
            'image' => $hotel->hero_image ? url(Storage::disk('public')->url($hotel->hero_image)) : null,
            'telephone' => $hotel->phone,
            'email' => $hotel->email,
            'checkinTime' => $hotel->check_in_time,
            'checkoutTime' => $hotel->check_out_time,
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $hotel->t('address'),
                'addressLocality' => $hotel->t('city'),
                'addressRegion' => $hotel->t('region'),
                'addressCountry' => $hotel->country_code,
            ]),
            'geo' => $hotel->latitude ? [
                '@type' => 'GeoCoordinates',
                'latitude' => $hotel->latitude,
                'longitude' => $hotel->longitude,
                'elevation' => $hotel->altitude_m,
            ] : null,
            'numberOfRooms' => $hotel->rooms()->count() ?: null,
            'brand' => ['@type' => 'Brand', 'name' => Setting::text('brand.name', 'Tenir Hotels')],
        ]);
    }

    public static function organizationSchema(): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => Setting::text('brand.name', 'Tenir Hotels'),
            'url' => url('/'),
            'email' => Setting::get('contacts.email'),
            'telephone' => Setting::get('contacts.phone'),
            'sameAs' => array_values(array_filter(Setting::get('social', []) ?: [])),
        ]);
    }
}
