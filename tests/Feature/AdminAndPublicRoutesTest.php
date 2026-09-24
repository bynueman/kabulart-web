<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class AdminAndPublicRoutesTest extends TestCase
{
    /**
     * Test all public pages return 200 OK.
     */
    public function test_public_pages_return_successful_response(): void
    {
        $routes = ['/', '/profil', '/informasi', '/gallery', '/testimoni'];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    /**
     * Test admin login page returns 200 OK for guest.
     */
    public function test_admin_login_page_loads(): void
    {
        $response = $this->get('/adminpanel');
        $response->assertStatus(200);
        $response->assertSee('Admin Login');
    }

    /**
     * Test guest cannot access admin routes without authentication.
     */
    public function test_guest_is_redirected_from_admin_pages(): void
    {
        $protectedRoutes = [
            '/homeadmin',
            '/postsgalery',
            '/postsgalery/create',
            '/postsinformasi',
            '/postsinformasi/create',
            '/posttestimoni',
            '/posttestimoni/create',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/adminpanel');
        }
    }

    /**
     * Test authenticated user can access admin dashboard and CRUD pages.
     */
    public function test_authenticated_user_can_access_admin_pages(): void
    {
        $user = User::first() ?? User::factory()->make([
            'id' => 1,
            'name' => 'Admin Test',
            'email' => 'admin@test.local',
            'password' => Hash::make('password'),
        ]);

        $adminRoutes = [
            '/homeadmin',
            '/postsgalery',
            '/postsgalery/create',
            '/postsinformasi',
            '/postsinformasi/create',
            '/posttestimoni',
            '/posttestimoni/create',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertStatus(200);
        }
    }
}
