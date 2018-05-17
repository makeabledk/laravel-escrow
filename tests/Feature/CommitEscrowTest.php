<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use Illuminate\Support\Facades\Event;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Events\EscrowCommitted;
use Makeable\LaravelEscrow\Events\EscrowDeposited;
use Makeable\LaravelEscrow\Labels\AccountDeposit;
use Makeable\LaravelEscrow\Labels\EscrowDeposit;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Tests\FakePaymentGateway;

class CommitEscrowTest extends DatabaseTestCase
{
    use FakePaymentGateway;

    /** @test **/
    public function it_charges_deposit_when_committing()
    {
        $this->assertTrue($this->escrow->getBalance()->equals(Amount::zero()));

        $this->escrow->commit();

        $this->assertEquals($this->product->getDepositAmount()->get(), $this->escrow->getBalance()->get());
    }

    /** @test **/
    public function it_tries_to_withdraw_from_customer_before_charging_the_customers_credit_card()
    {
        $this->customer->deposit(new Amount(1000), $this->charge());

        $this->assertEquals(1, $this->escrow->customer->deposits()->count());
        $this->assertEquals(0, $this->escrow->customer->withdrawals()->count());
        $this->assertTrue($this->escrow->customer->getBalance()->equals(new Amount(1000)));

        $this->escrow->commit();
        $this->assertEquals(1, $this->escrow->customer->deposits()->count());
        $this->assertEquals(1, $this->escrow->customer->withdrawals()->count());
        $this->assertEquals((new Amount(750))->get(), $this->escrow->customer->getBalance()->get());
    }

    /** @test **/
    public function it_charges_customers_credit_card_when_insufficient_funds_available()
    {
        $this->customer->deposit(new Amount(100), $this->charge());

        $this->escrow->commit();

        $this->assertEquals(2, $this->customer->deposits()->count());
        $this->assertEquals(1, $this->customer->withdrawals()->count());
        $this->assertTrue($this->customer->deposits->get(1)->amount->equals(new Amount(150)));
    }

    /** @test **/
    public function it_associates_customer_charges_with_the_escrow()
    {
        $this->escrow->commit();

        $this->assertEquals($this->escrow->id, $this->customer->deposits()->first()->associated_escrow_id);
    }

    /** @test **/
    public function it_fails_to_commit_if_cant_charge_deposit()
    {
        app(PaymentGatewayContract::class)->shouldFail();

        $this->expectException(\Exception::class);
        $this->escrow->commit();
    }

    /** @test **/
    public function it_labels_transactions_when_charging_and_depositing()
    {
        $this->escrow->commit();

        $accountDeposit = $this->customer->deposits()->first();
        $this->assertInstanceOf(AccountDeposit::class, $accountDeposit->label());

        $escrowDeposit = $this->escrow->deposits()->first();
        $this->assertInstanceOf(EscrowDeposit::class, $escrowDeposit->label());
    }

    /** @test **/
    public function it_dispatches_events_when_committing()
    {
        Event::fake();

        $this->escrow->commit();

        Event::assertDispatched(EscrowDeposited::class);
        Event::assertDispatched(EscrowCommitted::class);
    }
}
