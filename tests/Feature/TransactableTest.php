<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Transaction;

class TransactableTest extends DatabaseTestCase
{
    /** @test **/
    public function it_can_deposit_funds()
    {
        $this->escrow->deposit(new Amount(100), $this->customer);

        $this->assertTrue($this->escrow->getBalance()->equals(new Amount(100)));
        $this->assertTrue($this->customer->getBalance()->equals(new Amount(-100)));
    }

    /** @test **/
    function it_associates_deposits_with_an_escrow()
    {
        $transaction = $this->escrow->deposit(new Amount(100), $this->customer);

        $this->assertEquals($this->escrow->id, $transaction->associated_escrow_id);
    }

    /** @test **/
    public function it_can_withdraw_funds()
    {
        $this->customer->withdraw(new Amount(100), $this->escrow);

        $this->assertTrue($this->escrow->getBalance()->equals(new Amount(100)));
        $this->assertTrue($this->customer->getBalance()->equals(new Amount(-100)));
    }

    /** @test **/
    function it_associates_withdrawals_with_an_escrow()
    {
        $transaction = $this->escrow->withdraw(new Amount(100), $this->provider);

        $this->assertEquals($this->escrow->id, $transaction->associated_escrow_id);
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
