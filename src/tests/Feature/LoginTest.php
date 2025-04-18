<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_requires_email()
    {
        $response = $this->post('/login', [
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    public function test_login_requires_password()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    public function test_login_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors();
    }
}
