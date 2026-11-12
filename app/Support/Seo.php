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
        public ?string $keywords = null,
        public string $type = 'website',
        public ?string $imageAlt = null,
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
            keywords: $model->seoKeywords(),
            imageAlt: $title,
        );
    }

    public function withSchema(array $schema): self
    {
        $this->schema[] = $schema;

        return $this;
    }

    public function withKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function ofType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function metaKeywords(): ?string
    {
        $text = $this->keywords ?: Setting::text('seo.default_keywords');

        return $text ? str(strip_tags($text))->squish()->limit(250, '')->toString() : null;
    }

    /** schema.org/BreadcrumbList: Главная → [отель] → текущая страница. */
    public function breadcrumbs(array $trail): array
    {
        $items = [];
        foreach (array_values($trail) as $i => [$name, $url]) {
            $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => $url];
        }

        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    public function noindex(): self
    {
        $this->noindex = true;

        return $this;
    }

    /** Название сайта: в режиме «один отель» — название отеля, в режиме сети — бренд сети. */
    public static function siteName(): string
    {
        $hotel = Property::isSingle() ? Property::primary() : null;

        return $hotel?->t('name') ?: Setting::text('brand.name', 'Tenir Hotels');
    }

    /**
     * Единый формат: «Страница — Отель» (один отель) и «Страница — Отель | Бренд» / «Страница | Бренд» (сеть).
     */
    public function fullTitle(): string
    {
        $hotel = Property::isSingle() ? Property::primary() : null;

        if ($hotel) {
            $site = (string) $hotel->t('name');
            $title = $this->title ?: ($hotel->t('seo_title') ?: trim($site.($hotel->t('tagline') ? ' — '.$hotel->t('tagline') : '')));

            return str_contains($title, $site) ? $title : "$title — $site";
        }

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
        $path = $this->image ?: Setting::get('seo.og_image') ?: \App\Support\Property::primary()?->hero_image;

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
            'award' => $hotel->slug === config('tenir.recognition_hotel')
                ? (\App\Models\Recognition::forHome()->where('kind', 'award')->map(fn ($r) => trim($r->t('title').' — '.$r->source.' '.$r->year))->values()->all() ?: null)
                : null,
        ]);
    }

    public static function websiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => self::siteName(),
            'url' => url('/'),
            'inLanguage' => Locales::codes(),
        ];
    }

    public static function roomSchema($room, Hotel $hotel): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'HotelRoom',
            'name' => $room->t('name'),
            'description' => strip_tags((string) $room->t('summary')),
            'url' => $room->url(),
            'image' => $room->image ? url(Storage::disk('public')->url($room->image)) : null,
            'bed' => $room->t('bed'),
            'occupancy' => $room->max_guests ? ['@type' => 'QuantitativeValue', 'maxValue' => $room->max_guests] : null,
            'floorSize' => $room->size_m2 ? ['@type' => 'QuantitativeValue', 'value' => $room->size_m2, 'unitCode' => 'MTK'] : null,
            'amenityFeature' => array_map(fn ($a) => ['@type' => 'LocationFeatureSpecification', 'name' => $a, 'value' => true], $room->amenityList()) ?: null,
            'containedInPlace' => ['@type' => 'Hotel', 'name' => $hotel->t('name'), 'url' => $hotel->url()],
        ]);
    }

    public static function eventSchema($event): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event->t('title'),
            'description' => strip_tags((string) $event->t('summary')),
            'startDate' => $event->starts_on?->toDateString(),
            'endDate' => ($event->ends_on ?? $event->starts_on)?->toDateString(),
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'eventStatus' => 'https://schema.org/EventScheduled',
            'image' => $event->image ? url(Storage::disk('public')->url($event->image)) : null,
            'location' => array_filter([
                '@type' => 'Place',
                'name' => $event->t('place') ?: $event->hotel?->t('name') ?: Setting::text('brand.name', 'Tenir Hotels'),
                'address' => $event->hotel?->t('address') ?: null,
            ]),
            'url' => $event->url(),
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
