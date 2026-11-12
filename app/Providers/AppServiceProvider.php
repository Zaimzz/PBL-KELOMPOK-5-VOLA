<?php

namespace App\Providers;

use App\Models\Event;
use App\Policies\EventPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->numbers();
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token): MailMessage {
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

            return (new MailMessage)
                ->subject('Atur Ulang Kata Sandi Akun VOLA Anda')
                ->greeting('Halo, '.($notifiable->name ?? 'Pengguna VOLA').'!')
                ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun VOLA yang terhubung dengan alamat email ini.')
                ->action('Atur Ulang Kata Sandi', $resetUrl)
                ->line("Tautan pengaturan ulang kata sandi ini berlaku selama {$expireMinutes} menit.")
                ->line('Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini dan kata sandi akun Anda akan tetap aman.')
                ->salutation('Salam hangat, Tim VOLA');
        });

        // Explicitly register EventPolicy (auto-discovery also works,
        // but explicit registration is safer for testing environments)
        Gate::policy(Event::class, EventPolicy::class);
    }
}
