<?php

namespace Makeable\LaravelEscrow\Providers;

use Illuminate\Support\ServiceProvider;
use Makeable\LaravelEscrow\Adapters\Stripe\StripeCharge;
use Makeable\LaravelEscrow\Adapters\Stripe\StripePaymentProvider;
use Makeable\LaravelEscrow\Contracts\ChargeContract;
use Makeable\LaravelEscrow\Contracts\EscrowRepositoryContract;
use Makeable\LaravelEscrow\Contracts\PaymentProviderContract;
use Makeable\LaravelEscrow\Repositories\EscrowRepository;

class EscrowServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([__DIR__.'/../database/migrations/' => database_path('migrations')], 'migrations');
    }

    public function register()
    {
        $this->app->bind(ChargeContract::class, StripeCharge::class);
        $this->app->bind(EscrowRepositoryContract::class, EscrowRepository::class);
        $this->app->singleton(PaymentProviderContract::class, function () {
            return new StripePaymentProvider(config('services.stripe.secret'));
        });
    }
}
