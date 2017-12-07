<?php

namespace Makeable\LaravelEscrow\Tests\Feature\Interactions;

use Illuminate\Support\Facades\Event;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\EscrowStatus;
use Makeable\LaravelEscrow\Events\EscrowCancelled;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Interactions\CancelEscrow;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;

class CancelEscrowTest extends DatabaseTestCase
{
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
    public function it_refunds_the_charged_amount_to_the_customers_available_funds()
    {
        $this->escrow->commit()->cancel();

        $this->assertEquals($this->product->getDepositAmount()->get(), $this->customer->getBalance()->get());
        $this->assertEquals(0, $this->escrow->getBalance()->get());
    }


//    /** @test **/
//    public function it_fires_escrow_cancelled_event()
//    {
//        Event::fake();
//
//        $this->interact(CancelEscrow::class, $this->escrow);
//
//        Event::assertDispatched(EscrowCancelled::class, function ($event) {
//            return $event->escrow->id === $this->escrow->id;
//        });
//    }
//
//    /** @test **/
//    function it_()
//    {
//        Event::fake();
//
//        $this->escrow->forceUpdate(['status' => 1]);
//
//        Event::assertNotDispatched(EscrowCancelled::class);
//    }
}
