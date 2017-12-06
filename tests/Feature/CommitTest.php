<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Tests\Fakes\Product;
use Makeable\LaravelEscrow\Transaction;

class CommitTest extends DatabaseTestCase
{
    /** @test **/
    function it_charges_deposit_when_committing()
    {
        $this->assertTrue($this->escrow->getBalance()->equals(Amount::zero()));

        $this->escrow->commit();

        $this->assertTrue($this->escrow->getBalance()->equals($this->product->getDepositAmount()));
    }

    /** @test **/
    function it_fails_to_commit_if_cant_charge_deposit()
    {

    }
}
