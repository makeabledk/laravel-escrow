<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Makeable\LaravelEscrow\Contracts\EscrowRepositoryContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Tests\Fakes\Product;
use Makeable\LaravelEscrow\Transaction;

class CreateEscrowTest extends DatabaseTestCase
{
    /** @test **/
    function an_escrow_can_be_created_through_repository()
    {
        $this->assertInstanceOf(Escrow::class, $this->escrow);
    }
}
