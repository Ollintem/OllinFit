<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\PasswordResetCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function requestCode(User $user): string
    {
        $this->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect(route('password.code'));

        $code = null;
        Notification::assertSentTo($user, PasswordResetCode::class, function ($notification) use (&$code) {
            $code = $notification->code;

            return true;
        });

        return $code;
    }

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $this->get('/forgot-password')->assertOk();
    }

    public function test_reset_code_is_sent_and_stored_hashed(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $code = $this->requestCode($user);

        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
        $token = DB::table('password_reset_tokens')->where('email', $user->email)->value('token');
        $this->assertNotSame($code, $token);
        $this->assertTrue(Hash::check($code, $token));
    }

    public function test_unknown_email_gets_the_same_response_without_sending(): void
    {
        Notification::fake();

        $this->post('/forgot-password', ['email' => 'nadie@ollinfit.com'])
            ->assertRedirect(route('password.code'));

        Notification::assertNothingSent();
    }

    public function test_verify_screen_requires_a_requested_email(): void
    {
        $this->get('/forgot-password/verify')->assertRedirect(route('password.request'));
    }

    public function test_wrong_code_is_rejected(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $code = $this->requestCode($user);
        $wrong = $code === '000000' ? '111111' : '000000';

        $this->post('/forgot-password/verify', ['code' => $wrong])->assertSessionHasErrors('code');
        $this->get('/reset-password')->assertRedirect(route('password.request'));
    }

    public function test_code_is_invalidated_after_too_many_attempts(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $code = $this->requestCode($user);
        $wrong = $code === '000000' ? '111111' : '000000';

        for ($i = 0; $i < 5; $i++) {
            $this->post('/forgot-password/verify', ['code' => $wrong]);
        }

        $this->post('/forgot-password/verify', ['code' => $code])->assertSessionHasErrors('code');
    }

    public function test_expired_code_is_rejected(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $code = $this->requestCode($user);

        $this->travel(16)->minutes();

        $this->post('/forgot-password/verify', ['code' => $code])->assertSessionHasErrors('code');
    }

    public function test_password_cannot_be_reset_without_verifying(): void
    {
        $this->post('/reset-password', [
            'password' => 'NuevaClave1!',
            'password_confirmation' => 'NuevaClave1!',
        ])->assertRedirect(route('password.request'));
    }

    public function test_password_can_be_reset_with_valid_code(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $code = $this->requestCode($user);

        $this->post('/forgot-password/verify', ['code' => $code])->assertRedirect(route('password.reset'));
        $this->get('/reset-password')->assertOk();

        $this->post('/reset-password', [
            'password' => 'NuevaClave1!',
            'password_confirmation' => 'NuevaClave1!',
        ])->assertSessionHasNoErrors()->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('NuevaClave1!', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);

        // El código ya no puede reutilizarse
        $this->post('/forgot-password/verify', ['code' => $code])->assertRedirect(route('password.request'));
    }
}
