<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPasswordNotification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject(__('Reset Password Notification'))
            ->line(__('You are receiving this email because we received a password reset request for your account.'))
            ->action(__('Reset Password'), $url)
            ->line(__(
                'This password reset link will expire in :count minutes.',
                ['count' => config('auth.passwords.users.expire')]
            ))
            ->line(__('If you did not request a password reset, no further action is required.'));
    }

    /**
     * Get the reset URL for the given notifiable.
     *
     * @param mixed $notifiable
     */
    protected function resetUrl($notifiable): string
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
        $token = $this->token;
        $email = $notifiable->getEmailForPasswordReset();

        return "{$frontendUrl}/reset-password?token={$token}&email=".urlencode($email);
    }
}
