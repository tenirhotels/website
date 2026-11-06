<?php

namespace App\Models\Concerns;

use App\Support\Locales;

/**
 * Переводимые поля хранятся как JSON {"ru": "...", "kk": "...", ...}.
 * В админке редактируются через вкладки языков (App\Filament\Support\Translatable),
 * на сайте читаются через $model->t('title') с цепочкой fallback из config/tenir.php.
 */
trait HasTranslations
{
    public function initializeHasTranslations(): void
    {
        foreach ($this->translatable ?? [] as $attribute) {
            $this->mergeCasts([$attribute => 'array']);
        }
    }

    public function t(string $attribute, ?string $locale = null): ?string
    {
        $values = $this->getAttribute($attribute);

        if (! is_array($values)) {
            return $values;
        }

        foreach (Locales::chain($locale ?? app()->getLocale()) as $code) {
            $value = $values[$code] ?? null;
            if (filled($value)) {
                return $value;
            }
        }

        return null;
    }

    /** Есть ли перевод именно на этом языке (без fallback). */
    public function hasTranslation(string $attribute, string $locale): bool
    {
        $values = $this->getAttribute($attribute);

        return is_array($values) && filled($values[$locale] ?? null);
    }
}
