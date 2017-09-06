<?php

namespace Makeable\LaravelEscrow\Providers;

use Illuminate\Support\ServiceProvider;
use Makeable\LaravelEscrow\Adapters\Stripe\StripePaymentProvider;
use Makeable\LaravelEscrow\Contracts\EscrowPolicyContract;
use Makeable\LaravelEscrow\Contracts\EscrowRepositoryContract;
use Makeable\LaravelEscrow\Contracts\PaymentProviderContract;
use Makeable\LaravelEscrow\EscrowPolicy;
use Makeable\LaravelEscrow\Repositories\EscrowRepository;

class EscrowServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/');
    }

    public function register()
    {
        $this->app->bind(EscrowPolicyContract::class, EscrowPolicy::class);
        $this->app->bind(EscrowRepositoryContract::class, EscrowRepository::class);
        $this->app->singleton(PaymentProviderContract::class, function () {
            return new StripePaymentProvider(config('services.stripe.secret'));
        });
    }
}
