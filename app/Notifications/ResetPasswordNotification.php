<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Ganti URL ini sesuai dengan URL Frontend (React/Next.js) lo nanti

        return (new MailMessage)
            ->subject('Reset Password Project Catering') // Judul Email
            ->greeting('Halo, ' . $notifiable->fullname . '!') // Sapaan pake nama dari DB
            ->line('Lo dapet email ini karena kami menerima permintaan reset password buat akun lo.')
            ->action('Reset Password Sekarang', $this->url) // Tombol di email
            ->line('Kalau lo ngerasa nggak minta reset password, abaikan aja email ini.')
            ->salutation('Salam, Tim Developer Catering');
    }
}