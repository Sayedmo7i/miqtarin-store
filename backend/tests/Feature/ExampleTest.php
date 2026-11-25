<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/identity')
            ->assertStatus(200)
            ->assertSee('Mo7i Store');

        $this->get('/admin/login')
            ->assertStatus(200)
            ->assertSee('Admin Login');
    }

    public function test_brand_assets_and_fallback_logo_exist(): void
    {
        $assets = [
            'assets/brand/logo.svg',
            'assets/brand/icon.svg',
            'assets/brand/logo.jpg',
            'assets/brand/icon.jpg',
            'assets/img/default_full_logo.png',
        ];

        foreach ($assets as $asset) {
            $this->assertFileExists(public_path($asset));
        }
    }
}
