<?php

namespace Makeable\LaravelEscrow\Providers;

use Illuminate\Support\ServiceProvider;
use Makeable\LaravelCurrencies\CurrenciesServiceProvider;
use Makeable\LaravelEscrow\Adapters\Stripe\StripePaymentGateway;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Contracts\SalesAccountContract;
use Makeable\LaravelEscrow\SalesAccount;
use Makeable\QueryKit\QueryKitServiceProvider;

class EscrowServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/');
    }

    public function register()
    {
        $this->app->register(CurrenciesServiceProvider::class);
        $this->app->register(QueryKitServiceProvider::class);
        $this->app->singleton(PaymentGatewayContract::class, function () {
            return new StripePaymentGateway(config('services.stripe.secret'));
        });
        $this->app->singleton(SalesAccountContract::class, SalesAccount::class);
    }
}
