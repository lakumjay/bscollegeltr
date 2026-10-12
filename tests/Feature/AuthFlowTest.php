<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_root_redirects_guests_to_login()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_login_page_uses_dynamic_branding_and_standard_form()
    {
        Cache::put('college_profile', (object) [
            'college_name' => 'Dynamic Test College',
            'logo' => 'fee_structure.png',
        ], 300);

        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee('Dynamic Test College')
            ->assertSee(asset('fee_structure.png'), false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('action="' . route('login') . '"', false)
            ->assertDontSee('aksinghcollege.org/uploads/banner_1770371640.jpg', false)
            ->assertDontSee('aksinghcollege.org/uploads/logo_1770371649.jpg', false);
    }

    public function test_sensitive_legacy_auth_routes_are_not_available()
    {
        $this->get('/update-admin-password')->assertNotFound();
        $this->get('/register')->assertNotFound();
        $this->get('/logout')->assertStatus(405);
    }

    public function test_login_requires_email_and_password()
    {
        $this->from('/login')
            ->post('/login', [])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_admin_pages_redirect_guests_to_login()
    {
        $this->get('/admin/profile')->assertRedirect('/login');
    }
}
