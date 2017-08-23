<?php

namespace Makeable\LaravelEscrow\Providers;

use Illuminate\Support\ServiceProvider;
use Makeable\LaravelEscrow\Contracts\EscrowRepositoryContract;
use Makeable\LaravelEscrow\Contracts\TransactionContract;
use Makeable\LaravelEscrow\Repositories\EscrowRepository;
use Makeable\LaravelEscrow\Transaction;

class EscrowServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([__DIR__.'/../database/migrations/' => database_path('migrations')], 'migrations');
    }

    public function register()
    {
        $this->app->bind(TransactionContract::class, Transaction::class);
        $this->app->bind(EscrowRepositoryContract::class, EscrowRepository::class);
    }
}
