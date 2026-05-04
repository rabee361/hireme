<?php

namespace Tests\Feature\Dashboard;

use App\Livewire\Auth\AdminLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_dashboard_login_page(): void
    {
        $this->get(route('dashboard.companies'))
            ->assertRedirect(route('dashboard.login'));
    }

    public function test_verified_admins_can_log_in_through_the_livewire_login_screen(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => 'Password123!',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        Livewire::test(AdminLogin::class)
            ->set('email', $admin->email)
            ->set('password', 'Password123!')
            ->set('remember', true)
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard.companies'));

        $this->assertAuthenticatedAs($admin, 'web');
    }

    public function test_non_admin_users_cannot_open_dashboard_pages(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer, 'web')
            ->get(route('dashboard.companies'))
            ->assertForbidden();
    }

    public function test_companies_dashboard_page_renders_for_admins(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'web')
            ->get(route('dashboard.companies'))
            ->assertOk()
            ->assertSee('سجل الشركات')
            ->assertSee('قائمة الشركات');
    }
}