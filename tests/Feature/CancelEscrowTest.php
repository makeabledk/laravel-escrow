<?php

namespace Makeable\LaravelEscrow\Tests\Feature\Interactions;

use Illuminate\Support\Facades\Event;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\EscrowStatus;
use Makeable\LaravelEscrow\Events\EscrowCancelled;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Labels\AccountPayout;
use Makeable\LaravelEscrow\Labels\EscrowDepositRefund;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Tests\FakePaymentGateway;

class CancelEscrowTest extends DatabaseTestCase
{
    use FakePaymentGateway;

    /** @test **/
    public function it_can_cancel_open_or_committed_escrows()
    {
        $this->assertTrue($this->escrow()->cancel()->checkStatus(new EscrowStatus('cancelled')));
        $this->assertTrue($this->escrow()->commit()->cancel()->checkStatus(new EscrowStatus('cancelled')));
    }

    /** @test **/
    public function it_cannot_cancel_released_or_already_cancelled_escrows()
    {
        $this->expectException(IllegalEscrowAction::class);
        $this->escrow()->commit()->release()->cancel();

        $this->expectException(IllegalEscrowAction::class);
        $this->escrow()->commit()->cancel()->cancel();
    }

    /** @test **/
    public function it_fails_to_cancel_if_cannot_perform_refund()
    {
        app(PaymentGatewayContract::class)->shouldFail();

        $this->expectException(\Exception::class);
        $this->escrow->commit()->cancel();
    }

    /** @test **/
    public function it_refunds_the_original_charge_per_default()
    {
        $this->escrow->commit()->cancel();

        $this->assertEquals(2, $this->customer->deposits()->count());
        $this->assertEquals(2, $this->customer->withdrawals()->count());

        $this->assertEquals(0, $this->customer->getBalance()->get());
    }

    /** @test **/
    public function it_can_omit_refunding_the_original_charge_keeping_it_for_the_customers_available_funds()
    {
        $this->escrow->commit()->cancel(false);

        $this->assertEquals(2, $this->customer->deposits()->count());
        $this->assertEquals(1, $this->customer->withdrawals()->count());

        $this->assertEquals($this->product->getDepositAmount()->get(), $this->customer->getBalance()->get());
    }

    /** @test **/
    public function it_labels_transactions_when_cancelling_and_refunding()
    {
        $this->escrow->commit()->cancel();

        $refundedDeposit = $this->customer->deposits()->latest('id')->first();
        $this->assertInstanceOf(EscrowDepositRefund::class, $refundedDeposit->label());

        $paidOutDeposit = $this->customer->withdrawals()->latest('id')->first();
        $this->assertInstanceOf(AccountPayout::class, $paidOutDeposit->label());
    }

    /** @test **/
    public function it_fires_escrow_cancelled_event()
    {
        Event::fake();

        $this->escrow()->cancel();

        Event::assertDispatched(EscrowCancelled::class);
    }
}
