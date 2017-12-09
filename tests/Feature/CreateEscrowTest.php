<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Tests\FakePaymentGateway;
use Makeable\LaravelEscrow\Tests\Fakes\Customer;
use Makeable\LaravelEscrow\Tests\Fakes\Provider;

class CreateEscrowTest extends DatabaseTestCase
{
    use FakePaymentGateway;

    /** @test **/
    public function an_escrow_can_be_created_through_repository()
    {
        $this->assertInstanceOf(Escrow::class, $this->escrow);
    }

    /** @test **/
    public function it_can_create_an_escrow_through_the_helper_method()
    {
        $escrow = $this->product->escrow(
            factory(Customer::class)->create(),
            factory(Provider::class)->create()
        );

        $this->assertInstanceOf(Escrow::class, $escrow);
    }

    /** @test **/
    public function it_can_find_an_existing_escrow()
    {
        $this->assertTrue(
            $this->product->escrow($this->customer, $this->provider)->is($this->escrow)
        );
    }
}
