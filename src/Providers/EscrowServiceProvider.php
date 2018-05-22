<?php

namespace Makeable\LaravelEscrow\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Makeable\LaravelCurrencies\CurrenciesServiceProvider;
use Makeable\LaravelEscrow\Adapters\Stripe\StripePaymentGateway;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Contracts\SalesAccountContract;
use Makeable\LaravelEscrow\Events\RefundCreated;
use Makeable\LaravelEscrow\Jobs\CreateReversedTransaction;
use Makeable\LaravelEscrow\Repositories\InvoiceDocumentRepository;
use Makeable\LaravelEscrow\SalesAccount;
use Makeable\LaravelEscrow\Transaction;
use Makeable\LaravelEscrow\TransactionObserver;
use Makeable\LaravelEscrow\TransactionTypes\AccountPayout;
use Makeable\QueryKit\QueryKitServiceProvider;

class EscrowServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! class_exists('CreateEscrowsTable')) {
            $this->publishes([
                __DIR__.'/../../database/migrations/create_escrows_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_escrows_table.php'),
                __DIR__.'/../../database/migrations/create_escrow_transactions_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time() + 1).'_create_escrow_transactions_table.php'),
            ], 'migrations');
        }

        if (! class_exists('AddLabelToEscrowTransactionsTable')) {
            $this->publishes([
                __DIR__.'/../../database/migrations/add_type_to_escrow_transactions_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_add_type_to_escrow_transactions_table.php'),
            ], 'migrations');
        }

        $this->mergeConfigFrom(__DIR__.'/../../config/laravel-escrow.php', 'laravel-escrow');
        $this->publishes([__DIR__.'/../../config/laravel-escrow.php' => config_path('laravel-escrow.php')], 'config');

        Event::listen(RefundCreated::class, function ($event) {
            CreateReversedTransaction::dispatch($event->refundable, $event->refund, app(AccountPayout::class));
        });

        Transaction::observe(app(TransactionObserver::class));
    }

    public function register()
    {
        $this->app->register(CurrenciesServiceProvider::class);
        $this->app->register(QueryKitServiceProvider::class);
        $this->app->singleton(InvoiceDocumentRepository::class);
        $this->app->singleton(PaymentGatewayContract::class, function () {
            return new StripePaymentGateway(config('services.stripe.secret'));
        });
        $this->app->singleton(SalesAccountContract::class, SalesAccount::class);
    }
}
