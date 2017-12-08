<?php

namespace Makeable\LaravelEscrow\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Repositories\EscrowRepository;
use Makeable\LaravelEscrow\Tests\Fakes\Customer;
use Makeable\LaravelEscrow\Tests\Fakes\Product;
use Makeable\LaravelEscrow\Tests\Fakes\Provider;
use Makeable\LaravelStripeObjects\StripeCharge;
use Stripe\Charge;

class DatabaseTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Escrow
     */
    protected $escrow;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var Provider
     */
    protected $provider;

    public function setUp()
    {
        parent::setUp();

        $this->escrow();
    }

    /**
     * @return Escrow
     */
    public function escrow()
    {
        return $this->escrow = app(EscrowRepository::class)->create(
            $this->product = Product::create([]),
            $this->customer = factory(Customer::class)->create(),
            $this->provider = factory(Provider::class)->create()
        );
    }

    /**
     * @return \Makeable\LaravelStripeObjects\StripeObject
     */
    public function charge()
    {
        return StripeCharge::createFromObject(new Charge(1));
    }
}
