<?php

namespace Makeable\LaravelEscrow\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Interactions\Interact;
use Makeable\LaravelEscrow\Providers\EscrowServiceProvider;
use Makeable\LaravelEscrow\Contracts\SalesAccountContract;
use Makeable\LaravelEscrow\Tests\Fakes\Customer;
use Makeable\LaravelEscrow\Tests\Fakes\PaymentGateway;
use Makeable\LaravelEscrow\Tests\Fakes\Provider;
use Makeable\LaravelEscrow\Transactable;
use Makeable\LaravelEscrow\Transaction;
use Makeable\LaravelEscrow\Transfer;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpFactories($this->app);

//        if (property_exists($this, 'migrateDatabase')) {
//            $this->artisan('migrate');
//        }

        // Bind a dummy sales account
        app()->singleton(SalesAccountContract::class, function () {
            return new class() {
                use Transactable;

                public function getKey(){
                    return rand();
                }

                public function getMorphClass(){
                    return get_class($this);
                }
            };
        });

        app()->singleton(PaymentGatewayContract::class, function () {
            return new PaymentGateway();
        });

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
                'amount' => rand(100, 1000),
                'currency_code' => Amount::zero()->currency()->getCode(),
            ];
        });

        $app->make(Factory::class)->define(Transfer::class, function ($faker) {
            return [
                'source_type' => 'foo',
                'source_id' => 1,
                'amount' => rand(100, 1000),
                'currency_code' => Amount::zero()->currency()->getCode(),
            ];
        });

        $app->make(Factory::class)->define(Customer::class, function ($faker) {
            return [
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => bcrypt('foo'),
            ];
        });

        $app->make(Factory::class)->define(Provider::class, function ($faker) {
            return [
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => bcrypt('foo'),
            ];
        });
    }

    protected function interact($class, ...$args)
    {
        return Interact::call($class, ...$args);
    }
}
