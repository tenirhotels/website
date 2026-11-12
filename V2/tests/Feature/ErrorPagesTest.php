<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_404_is_branded_and_localized(): void
    {
        $this->get('/ru/net-takoy-stranitsy')->assertNotFound()->assertSee('Здесь тропы нет')->assertSee('На главную');
        $this->get('/kk/joq')->assertNotFound()->assertSee('Мұнда соқпақ жоқ');
        $this->get('/en/nope')->assertNotFound()->assertSee('No trail here');
        $this->get('/zh/nope')->assertNotFound()->assertSee('这里没有山路');
        // Без языка в адресе — по языку браузера.
        $this->get('/=', ['Accept-Language' => 'en'])->assertNotFound()->assertSee('No trail here');
    }

    public function test_other_status_pages_render(): void
    {
        foreach ([403, 419, 429, 500, 503] as $code) {
            $this->assertNotEmpty(view('errors.'.$code)->render());
        }
    }
}
