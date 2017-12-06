<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Makeable\LaravelEscrow\Contracts\EscrowRepository;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Tests\Fakes\Customer;
use Makeable\LaravelEscrow\Tests\Fakes\Product;
use Makeable\LaravelEscrow\Tests\Fakes\Provider;
use Makeable\LaravelEscrow\Transaction;

class CreateEscrowTest extends DatabaseTestCase
{
    /** @test **/
    function an_escrow_can_be_created_through_repository()
    {
        $this->assertInstanceOf(Escrow::class, $this->escrow);
    }

    /** @test **/
    function it_can_create_an_escrow_through_the_helper_method()
    {
        $escrow = $this->product->escrow(
            factory(Customer::class)->create(),
            factory(Provider::class)->create()
        );

        $this->assertInstanceOf(Escrow::class, $escrow);
    }

    /** @test **/
    function it_can_find_an_existing_escrow()
    {
        $this->assertTrue(
            $this->product->escrow($this->customer, $this->provider)->is($this->escrow)
        );
    }
}
