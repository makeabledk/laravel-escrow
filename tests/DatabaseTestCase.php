<?php

namespace Makeable\LaravelEscrow\Tests;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Tests\Fakes\Product;

class DatabaseTestCase extends TestCase
{
    use DatabaseTransactions;

    protected $migrateDatabase = true;

    protected $escrow, $product, $customer, $provider;

    public function setUp()
    {
        parent::setUp();

        $this->product = Product::create([]);
        $this->customer = factory(User::class)->create();
        $this->provider = factory(User::class)->create();
        $this->escrow = Escrow::init($this->product, $this->customer, $this->provider);
    }
}
