<?php

namespace Makeable\LaravelEscrow\Tests;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Makeable\LaravelEscrow\Adapters\Stripe\StripeCharge;
use Makeable\LaravelEscrow\Adapters\Stripe\StripeTransactionSource;
use Makeable\LaravelEscrow\Interactions\Interact;
use Makeable\LaravelEscrow\Providers\EscrowServiceProvider;
use Makeable\LaravelEscrow\Transaction;
use Makeable\ValueObjects\Amount\Amount;
use Makeable\ValueObjects\Amount\TestCurrency;

class TestCase extends BaseTestCase
{
    /**
     */
    public function setUp()
    {
        parent::setUp();

        $this->setUpFactories($this->app);

        if(property_exists($this, 'migrateDatabase')) {
            $this->artisan('migrate');
        }

        // Put Amount in test mode so we don't need a currency implementation
        Amount::test();
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        putenv('APP_ENV=testing');
        putenv('APP_DEBUG=true');
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');

        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->useEnvironmentPath(__DIR__.'/..');
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $app->register(EscrowServiceProvider::class);
        $app->afterResolving('migrator', function ($migrator) {
            $migrator->path(__DIR__.'/migrations/');
        });

        return $app;
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpFactories($app)
    {
        $app->make(Factory::class)->define(Transaction::class, function ($faker) {
            return [
                'source_type' => 'foo',
                'source_id' => 1,
                'destination_type' => 'bar',
                'destination_id' => 1,
                'transfer_type' => array_random([StripeCharge::class, StripeTransactionSource::class]),
                'amount' => rand(100, 1000),
                'currency_code' => array_rand(TestCurrency::$currencies)
            ];
        });
    }

    protected function interact($class, ...$args)
    {
        return Interact::call($class, ...$args);
    }
}