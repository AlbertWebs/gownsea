<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_and_view_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@gownsea.com',
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->post('/admin/login', [
            'email' => 'admin@gownsea.com',
            'password' => 'password',
        ])->assertRedirect('/admin');

        $this->actingAs($user)->get('/admin')->assertOk()->assertSee('Dashboard');
    }

    public function test_catalogue_manager_cannot_access_users(): void
    {
        $user = User::factory()->create([
            'role' => 'catalogue_manager',
            'status' => 'active',
        ]);

        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_product_update_is_reflected_on_storefront(): void
    {
        $this->seed();
        $product = Product::query()->where('slug', 'undergraduate-academic-hoods')->firstOrFail();
        $product->update(['name' => 'Undergraduate Hoods Admin Title']);

        $this->get('/our-products/undergraduate-academic-hoods')
            ->assertOk()
            ->assertSee('Undergraduate Hoods Admin Title');
    }
}
