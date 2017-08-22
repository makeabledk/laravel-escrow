<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Support\ServiceProvider;

class EscrowServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }
}