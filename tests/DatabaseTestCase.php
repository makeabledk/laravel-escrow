<?php

namespace Makeable\LaravelEscrow\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Contracts\EscrowRepositoryContract;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Tests\Fakes\Customer;
use Makeable\LaravelEscrow\Tests\Fakes\Product;
use Makeable\LaravelEscrow\Tests\Fakes\Provider;

class DatabaseTestCase extends TestCase
{
    use RefreshDatabase;

    protected $migrateDatabase = true;

    /**
     * @var Escrow
     */
    protected $escrow;

    /**
     * @var EscrowableContract
     */
    protected $product;

    /**
     * @var CustomerContract
     */
    protected $customer;

    /**
     * @var ProviderContract
     */
    protected $provider;


    public function setUp()
    {
        parent::setUp();

        $this->escrow = app(EscrowRepositoryContract::class)->create(
            $this->product = Product::create([]),
            $this->customer = factory(Customer::class)->create(),
            $this->provider = factory(Provider::class)->create()
        );
    }
}
