<?php

namespace Makeable\LaravelEscrow\Providers;

use Illuminate\Support\ServiceProvider;
use Makeable\LaravelEscrow\Adapters\Stripe\StripePaymentProvider;
use Makeable\LaravelEscrow\Contracts\PaymentProviderContract;

class EscrowServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/');
    }

    public function register()
    {
        $this->app->singleton(PaymentProviderContract::class, function () {
            return new StripePaymentProvider(config('services.stripe.secret'));
        });
    }
}
