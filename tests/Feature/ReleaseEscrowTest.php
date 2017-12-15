<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use Illuminate\Support\Facades\Event;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Contracts\SalesAccountContract;
use Makeable\LaravelEscrow\Events\EscrowDeposited;
use Makeable\LaravelEscrow\Events\EscrowFunded;
use Makeable\LaravelEscrow\Events\EscrowReleased;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Tests\FakePaymentGateway;

class ReleaseEscrowTest extends DatabaseTestCase
{
    use FakePaymentGateway;

    /** @test **/
    public function it_cant_release_until_committed()
    {
        $this->expectException(IllegalEscrowAction::class);
        $this->escrow->release();

        $this->escrow->commit()->release();
        $this->assertEquals('released', $this->escrow->status->get());
    }

    /** @test **/
    public function it_fails_to_release_if_cant_charge_full_amount()
    {
        $this->escrow->commit();

        app(PaymentGatewayContract::class)->shouldFail();

        $this->expectException(\Exception::class);
        $this->escrow->release();
    }

    /** @test **/
    public function it_charges_the_remaining_amount()
    {
        $this->escrow->commit()->release();

        $this->assertTrue($this->escrow->deposits->get(0)->amount->equals($this->product->getDepositAmount()));
        $this->assertTrue($this->escrow->deposits->get(1)->amount->equals($this->product->getCustomerAmount()->subtract($this->product->getDepositAmount())));
    }

    /** @test **/
    public function it_transfers_funds_to_provider_and_sales_account()
    {
        $this->escrow->commit()->release();

        list($providerAmount, $feeAmount) = [
            $this->product->getProviderAmount(),
            $this->product->getCustomerAmount()->subtract($this->product->getProviderAmount()),
        ];

        dd(app(SalesAccountContract::class)->getBalance());

        $this->assertTrue($this->escrow->getBalance()->equals(Amount::zero()));
        $this->assertTrue($this->provider->getBalance()->equals($providerAmount));

        $this->assertTrue(app(SalesAccountContract::class)->getBalance()->equals($feeAmount));
    }

    /** @test **/
    public function it_dispatches_events_when_releasing()
    {
        $this->escrow->commit();

        Event::fake();

        $this->escrow->release();

        Event::assertDispatched(EscrowDeposited::class);
        Event::assertDispatched(EscrowFunded::class);
        Event::assertDispatched(EscrowReleased::class);
    }
}
