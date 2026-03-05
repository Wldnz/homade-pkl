<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
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
    public function toMail(User $notifiable): MailMessage
    {
        $url = route('user.reset-password', [ 'token' => $this->token, 'email' => $notifiable->email ]);
        return (new MailMessage)
            ->subject('Permintaan Reset Password')
            ->greeting('Halo, Pelanggan Kami Yang Terhomat!')
            ->line('Informasi yang ingin kami berikan saat ini adalah terkait permintaan untuk melakukan reset kata sandi')
            ->line('Jika anda tidak merasa melakukan silahkan abaikan saja pemberitahuan ini')
            ->action('Reset Kata Sandi Sekarang',$url) 
            ->salutation('Hormat Kami, Homade');
    }
}