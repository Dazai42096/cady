<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_are_accessible(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/about')->assertStatus(200);
        $this->get('/services')->assertStatus(200);
        $this->get('/contact')->assertStatus(200);
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => Role::ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertStatus(200);
    }

    public function test_customer_cannot_access_dashboard(): void
    {
        $customer = User::factory()->create([
            'role' => Role::CUSTOMER,
            'is_active' => true,
        ]);

        $this->actingAs($customer)
            ->get('/dashboard')
            ->assertStatus(403);
    }
}
