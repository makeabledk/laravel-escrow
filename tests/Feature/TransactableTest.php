<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\TransactionTypes\AccountDeposit;
use Makeable\LaravelEscrow\TransactionTypes\AccountPayout;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Tests\FakePaymentGateway;
use Makeable\LaravelEscrow\Transaction;

class TransactableTest extends DatabaseTestCase
{
    use FakePaymentGateway;

    /** @test **/
    public function it_can_deposit_funds()
    {
        $this->escrow->deposit(new Amount(100), $this->customer);

        $this->assertTrue($this->escrow->getBalance()->equals(new Amount(100)));
        $this->assertTrue($this->customer->getBalance()->equals(new Amount(-100)));
    }

    /** @test **/
    public function it_associates_deposits_with_an_escrow()
    {
        $transaction = $this->escrow->deposit(new Amount(100), $this->customer);

        $this->assertEquals($this->escrow->id, $transaction->associated_escrow_id);
    }

    /** @test **/
    public function a_callable_can_be_passed_when_depositing()
    {
        $transaction = $this->escrow->deposit(new Amount(100), $this->customer, function (Transaction $transaction) {
            $transaction->setType(AccountDeposit::class);
        });

        $this->assertInstanceOf(AccountDeposit::class, $transaction->type());
    }

    /** @test **/
    public function it_can_withdraw_funds()
    {
        $this->customer->withdraw(new Amount(100), $this->escrow);

        $this->assertTrue($this->escrow->getBalance()->equals(new Amount(100)));
        $this->assertTrue($this->customer->getBalance()->equals(new Amount(-100)));
    }

    /** @test **/
    public function it_associates_withdrawals_with_an_escrow()
    {
        $transaction = $this->escrow->withdraw(new Amount(100), $this->provider);

        $this->assertEquals($this->escrow->id, $transaction->associated_escrow_id);
    }

    /** @test **/
    public function a_callable_can_be_passed_when_withdrawing()
    {
        $transaction = $this->escrow->withdraw(new Amount(100), $this->provider, function (Transaction $transaction) {
            $transaction->setType(new AccountPayout); // accepts an instance too
        });

        $this->assertInstanceOf(AccountPayout::class, $transaction->type());
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

    /** @test */
    public function all_transactions_can_be_queried_too()
    {
        $withdrawal = factory(Transaction::class)->make();
        $deposit = factory(Transaction::class)->make();

        $this->escrow->withdrawals()->save($withdrawal);
        $this->escrow->deposits()->save($deposit);

        $this->assertEquals(2, $this->escrow->transactions()->count());
    }
}
