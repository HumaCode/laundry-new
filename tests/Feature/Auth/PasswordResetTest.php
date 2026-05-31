<?php

use App\Models\User;
use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token'                 => $notification->token,
            'email'                 => $user->email,
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
});

test('forgot password link request fails if email does not exist', function () {
    $response = $this->post('/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);
    expect(session()->get('errors')->first('email'))->toBe('Alamat email tidak terdaftar dalam sistem kami.');
});

test('forgot password requests are rate limited after 3 attempts', function () {
    $user = User::factory()->create();

    // 3 successful hits (to trigger rate limit limit)
    for ($i = 0; $i < 3; $i++) {
        $this->post('/forgot-password', ['email' => $user->email]);
    }

    // 4th hit should be blocked
    $response = $this->post('/forgot-password', ['email' => $user->email]);

    $response->assertSessionHasErrors(['email']);
    expect(session()->get('errors')->first('email'))->toContain('Terlalu banyak permintaan reset password');
});
