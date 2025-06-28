<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        try {
            $verificationUrl = $this->verificationUrl($notifiable);

            return (new MailMessage)
                ->subject('Verify Email Address')
                ->greeting('Hello ' . $notifiable->fname . '!')
                ->line('Thank you for registering with our cloud kitchen service. Please click the button below to verify your email address.')
                ->action('Verify Email Address', $verificationUrl)
                ->line('This verification link will expire in 60 minutes.')
                ->line('If you did not create an account, no further action is required.');
        } catch (\Exception $e) {
            Log::error('Email verification failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function verificationUrl($notifiable): string
    {
        try {
            return URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Verification URL generation failed: ' . $e->getMessage());
            throw $e;
        }
    }
} 