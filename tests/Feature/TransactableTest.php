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

class TransactableTest extends DatabaseTestCase
{
    /** @test **/
    function it_can_deposit_funds()
    {
        $this->escrow->deposit(new Amount(100), $this->customer);

        $this->assertTrue($this->escrow->getBalance()->equals(new Amount(100)));
        $this->assertTrue($this->customer->getBalance()->equals(new Amount(-100)));
    }

    /** @test **/
    function it_can_withdraw_funds()
    {
        $this->customer->withdraw(new Amount(100), $this->escrow);

        $this->assertTrue($this->escrow->getBalance()->equals(new Amount(100)));
        $this->assertTrue($this->customer->getBalance()->equals(new Amount(-100)));
    }

    /** @test */
    public function balance_consists_of_deposits_and_withdrawals()
    {
        $withdrawal = factory(Transaction::class)->make();
        $deposit = factory(Transaction::class)->make();

        $this->escrow->withdrawals()->save($withdrawal);
        $this->escrow->deposits()->save($deposit);

        $this->assertTrue(
            $this->escrow->getBalance()->equals($deposit->amount->subtract($withdrawal->amount))
        );
    }
}
