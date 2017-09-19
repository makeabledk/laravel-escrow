<?php

namespace Makeable\LaravelEscrow\Tests;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Contracts\EscrowRepositoryContract;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Tests\Fakes\Product;

class DatabaseTestCase extends TestCase
{
    use DatabaseTransactions;

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

        $this->product = Product::create([]);
        $this->customer = factory(User::class)->create();
        $this->provider = factory(User::class)->create();
        $this->escrow = app(EscrowRepositoryContract::class)->create($this->product, $this->customer, $this->provider);
    }
}
