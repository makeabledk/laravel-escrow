<?php

namespace Makeable\LaravelEscrow\Providers;

use Illuminate\Support\ServiceProvider;
use Makeable\LaravelEscrow\Contracts\ChargeContract;
use Makeable\LaravelEscrow\Contracts\EscrowRepositoryContract;
use Makeable\LaravelEscrow\Contracts\TransactionContract;
use Makeable\LaravelEscrow\Repositories\EscrowRepository;
use Makeable\LaravelEscrow\Transaction;
use Makeable\StripeConnectEscrow\Stripe\StripeCharge;

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
    }
}
