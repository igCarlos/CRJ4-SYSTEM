<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

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
        Validator::replacer('unique', function ($message, $attribute, $rule, $parameters) {
            if ($attribute === 'email') {
                return 'Este correo ya está registrado.';
            }

            return $message;
        });
    }
}
