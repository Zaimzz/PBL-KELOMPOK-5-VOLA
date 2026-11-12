<?php

namespace App\Providers;

use App\Models\Event;
use App\Policies\EventPolicy;
use Illuminate\Support\Facades\Gate;
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
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->numbers();
        });

        // Explicitly register EventPolicy (auto-discovery also works,
        // but explicit registration is safer for testing environments)
        Gate::policy(Event::class, EventPolicy::class);
    }
}
