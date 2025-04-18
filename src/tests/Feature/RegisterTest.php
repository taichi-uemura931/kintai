<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_name_required()
    {
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $response->assertSessionHasErrors(['name']);
    }

    public function test_email_required()
    {
        $response = $this->post('/register', [
            'name' => 'テスト',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    public function test_password_min_length()
    {
        $response = $this->post('/register', [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => '123',
            'password_confirmation' => '123'
        ]);
        $response->assertSessionHasErrors(['password']);
    }
}
