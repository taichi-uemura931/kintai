<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Database\Seeders\StatusSeeder;
use App\Models\User;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(StatusSeeder::class);
    }

    public function test_verification_email_is_sent_upon_registration()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
            'status_id' => 1,
        ]);

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_user_can_verify_email()
    {
        $user = User::factory()->unverified()->create([
            'status_id' => 1,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect('/attendance');

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_unverified_user_cannot_access_protected_page()
    {
        $user = User::factory()->unverified()->create([
            'status_id' => 1,
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertRedirect('/email/verify');
    }
}
