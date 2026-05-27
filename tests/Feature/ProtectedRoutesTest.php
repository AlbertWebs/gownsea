<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProtectedRoutesTest extends TestCase
{
    public function test_protected_routes_are_accessible(): void
    {
        foreach (config('gownsea.protected_routes', []) as $route) {
            $this->get($route)->assertSuccessful();
        }
    }

    public function test_sitemap_is_accessible(): void
    {
        $response = $this->get('/sitemap.xml')
            ->assertSuccessful();

        $this->assertStringContainsString(
            'application/xml',
            (string) $response->headers->get('Content-Type')
        );
    }
}
