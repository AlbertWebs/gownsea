<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoAndNavigationTest extends TestCase
{
    public function test_key_public_routes_include_core_seo_tags(): void
    {
        $routes = [
            '/',
            '/about-us',
            '/contact-us',
            '/legal-attire',
            '/graduation-attire',
            '/church-wear',
            '/gown-for-hire',
            '/the-gown-journal',
            '/privacy-policy',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);

            $response->assertSuccessful();
            $response->assertSee('rel="canonical"', false);
            $response->assertSee('property="og:title"', false);
            $response->assertSee('property="og:description"', false);
            $response->assertSee('name="twitter:card"', false);
        }
    }

    public function test_homepage_contains_social_meta_tags(): void
    {
        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('property="og:site_name"', false);
        $response->assertSee('property="og:title"', false);
        $response->assertSee('name="twitter:card"', false);
        $response->assertSee('rel="canonical"', false);
    }

    public function test_header_contains_primary_navigation_items(): void
    {
        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('Graduation Attire');
        $response->assertSee('Legal Attire');
        $response->assertSee('Church Wear');
        $response->assertSee('The Gown Journal');
        $response->assertSee('Bulk Hire');
        $response->assertSee('Gown for Hire');
    }

    public function test_robots_file_exposes_sitemap_directive(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertNotFalse($robots);
        $this->assertStringContainsString('Sitemap: /sitemap.xml', (string) $robots);
    }
}
