<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Transfer;

class CommitTest extends DatabaseTestCase
{
    /** @test **/
    public function it_charges_deposit_when_committing()
    {
        $this->assertTrue($this->escrow->getBalance()->equals(Amount::zero()));

        $this->escrow->commit();

        $this->assertTrue($this->escrow->getBalance()->equals($this->product->getDepositAmount()));
    }

    /** @test **/
    public function it_tries_to_withdraw_from_customer_before_charging_the_customers_credit_card()
    {
        $this->customer->deposit(new Amount(1000), factory(Transfer::class)->create());

        $this->assertEquals(1, $this->customer->deposits()->count());
        $this->assertEquals(0, $this->customer->withdrawals()->count());
        $this->assertTrue($this->customer->getBalance()->equals(new Amount(1000)));

        $this->escrow->commit();
        $this->assertEquals(1, $this->customer->deposits()->count());
        $this->assertEquals(1, $this->customer->withdrawals()->count());
        $this->assertTrue($this->customer->getBalance()->equals(new Amount(750)));
    }

    /** @test **/
    public function it_charges_customers_credit_card_when_insufficient_funds_available()
    {
        $this->customer->deposit(new Amount(100), factory(Transfer::class)->create());

        $this->escrow->commit();

        $this->assertEquals(2, $this->customer->deposits()->count());
        $this->assertEquals(1, $this->customer->withdrawals()->count());
        $this->assertTrue($this->customer->deposits->get(1)->getAmount()->equals(new Amount(150)));
    }

    /** @test **/
    public function it_fails_to_commit_if_cant_charge_deposit()
    {
        app(PaymentGatewayContract::class)->shouldFail();

        $this->expectException(\Exception::class);
        $this->escrow->commit();
    }
}
