<?php

namespace App\Notifications\Auth;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    /**
     * The password reset token.
     */
    public function __construct(
        public readonly string $token
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expireMinutes = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        return (new MailMessage)
            ->subject('Reset Kata Sandi Akun Anda – LaundryPro')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun LaundryPro Anda.')
            ->line('Klik tombol di bawah ini untuk membuat kata sandi baru:')
            ->action('Reset Kata Sandi Sekarang', $url)
            ->line("Tautan ini hanya berlaku selama **{$expireMinutes} menit**.")
            ->line('Jika Anda tidak merasa membuat permintaan ini, abaikan email ini. Akun Anda tetap aman dan tidak ada perubahan yang dilakukan.')
            ->salutation('Salam hangat, **Tim LaundryPro** 🧺');
    }
}
