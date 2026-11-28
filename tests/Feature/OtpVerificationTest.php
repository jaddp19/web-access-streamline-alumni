<?php

namespace Tests\Feature;

use App\Mail\TemplatedMail;
use App\Models\Batch;
use App\Models\User;
use Database\Seeders\EmailSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OtpVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->seed(EmailSeeder::class);

        Role::firstOrCreate([
            'name' => 'alumni',
            'guard_name' => 'web',
        ]);
    }

    private function lastCode(): string
    {
        $mail = Mail::sent(TemplatedMail::class)->last();

        $this->assertNotNull($mail, 'No templated mail was sent.');

        preg_match('/\b(\d{6})\b/', $mail->bodyHtml, $matches);

        $this->assertNotEmpty($matches[1] ?? null, 'No 6-digit code found in the email body.');

        return $matches[1];
    }

    public function test_registration_requires_the_emailed_code_before_creating_the_account(): void
    {
        Mail::fake();
        Http::fake(['api.pwnedpasswords.com/*' => Http::response('', 200)]);

        Batch::create(['batch_name' => 2020]);

        $component = Livewire::test('auth::register');

        // Make the form look older than the 3-second bot threshold.
        $token = $component->get('formToken');
        session()->put('register.form.'.$token, now()->subSeconds(10)->timestamp);

        $component
            ->set('first_name', 'Test')
            ->set('middle_name', '')
            ->set('last_name', 'Alumni')
            ->set('email', 'otp-register@example.com')
            ->set('school_id', '2020-1234')
            ->set('password', 'StrongPass!2345')
            ->set('password_confirmation', 'StrongPass!2345')
            ->set('terms', true)
            ->call('sendRegistrationCode')
            ->assertSet('showOtpStep', true)
            ->assertSet('pendingEmail', 'otp-register@example.com');

        $this->assertDatabaseMissing('users', [
            'email' => 'otp-register@example.com',
        ]);

        $code = $this->lastCode();

        $component
            ->set('otpCode', $code)
            ->call('verifyRegistrationCode')
            ->assertRedirect(route('form'));

        $this->assertDatabaseHas('users', [
            'email' => 'otp-register@example.com',
            'school_id' => '2020-1234',
        ]);

        $user = User::where('email', 'otp-register@example.com')->first();

        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($user->hasRole('alumni'));
        $this->assertTrue(Hash::check('StrongPass!2345', $user->password));
    }

    public function test_registration_does_not_create_an_account_when_the_code_is_wrong(): void
    {
        Mail::fake();
        Http::fake(['api.pwnedpasswords.com/*' => Http::response('', 200)]);

        Batch::create(['batch_name' => 2020]);

        $component = Livewire::test('auth::register');

        $token = $component->get('formToken');
        session()->put('register.form.'.$token, now()->subSeconds(10)->timestamp);

        $component
            ->set('first_name', 'Wrong')
            ->set('last_name', 'Code')
            ->set('email', 'otp-wrong@example.com')
            ->set('school_id', '2020-5678')
            ->set('password', 'StrongPass!2345')
            ->set('password_confirmation', 'StrongPass!2345')
            ->set('terms', true)
            ->call('sendRegistrationCode')
            ->assertSet('showOtpStep', true);

        $component
            ->set('otpCode', '000000')
            ->call('verifyRegistrationCode')
            ->assertHasErrors('otpCode');

        $this->assertDatabaseMissing('users', [
            'email' => 'otp-wrong@example.com',
        ]);
    }

    public function test_forgot_password_requires_the_emailed_code_before_creating_a_reset_token(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'otp-reset@example.com',
            'school_id' => '2020-9999',
        ]);

        $component = Livewire::test('auth::login');

        $component
            ->call('openForgotModal')
            ->set('forgotEmail', $user->email)
            ->call('verifyForgotEmail')
            ->assertSet('forgotStep', 2)
            ->set('forgotSchoolId', $user->school_id)
            ->call('verifyForgotSchoolIdAndSendCode')
            ->assertSet('forgotStep', 3);

        $this->assertStringContainsString(
            $user->email,
            $component->get('forgotStatusMessage')
        );

        // No reset token exists until the code is verified.
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);

        $code = $this->lastCode();

        $component
            ->set('forgotCode', $code)
            ->call('verifyForgotCode')
            ->assertRedirectContains('/reset-password/');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }
}
