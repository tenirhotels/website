<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Support\Locales;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SourceLocaleSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_source_locale_defaults_to_config_and_follows_setting(): void
    {
        $this->assertSame('ru', Locales::source());

        Setting::put('site', ['source_locale' => 'en']);
        $this->assertSame('en', Locales::source());

        Setting::put('site', ['source_locale' => 'xx']); // неизвестный язык — игнорируется
        $this->assertSame('ru', Locales::source());
    }
}
