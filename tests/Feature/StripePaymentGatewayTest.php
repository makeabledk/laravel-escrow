<?php

namespace Makeable\LaravelEscrow\Tests\Feature\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelCurrencies\Currency;
use Makeable\LaravelCurrencies\TestCurrency;
use Makeable\LaravelEscrow\Adapters\Stripe\StripeCharge;
use Makeable\LaravelEscrow\Adapters\Stripe\StripePaymentGateway;
use Makeable\LaravelEscrow\Adapters\Stripe\StripeRefund;
use Makeable\LaravelEscrow\Adapters\Stripe\StripeTransfer;
use Makeable\LaravelEscrow\Adapters\Stripe\StripeTransferReversal;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Tests\Fakes\Customer;
use Makeable\LaravelEscrow\Tests\Fakes\Provider;
use Stripe\Token;

class StripePaymentGatewayTest extends DatabaseTestCase
{
    /** @test **/
    public function it_can_charge_a_customer()
    {
        $charge = $this->gateway()->charge(
            $this->validCustomer(),
            $amount = new Amount(25, 'USD'),
            $escrow = factory(Escrow::class)->create()
        );

        $this->assertInstanceOf(StripeCharge::class, $charge);
        $this->assertEquals($escrow->identifier, $charge->data['transfer_group']);
        $this->assertEquals(2500, $charge->data['amount']);
    }

    /** @test **/
    public function it_charges_a_minimum_of_5DKK()
    {
        $charge = $this->gateway()->charge(
            $this->validCustomer(),
            $amount = new Amount(0.1, 'USD'),
            $escrow = factory(Escrow::class)->create()
        );

        $this->assertInstanceOf(StripeCharge::class, $charge);
        $this->assertEquals($escrow->identifier, $charge->data['transfer_group']);
        $this->assertEquals((new Amount(5, 'DKK'))->convertTo(TestCurrency::fromCode('USD'))->toCents(), $charge->data['amount']);
    }

    /** @test **/
    public function it_can_pay_funds_to_connected_account()
    {
        // first put funds in account to transfer
        $this->gateway()->charge($this->validCustomer(), new Amount(25, 'USD'));

        $payout = $this->gateway()->pay(
            $this->validProvider(),
            $amount = new Amount(25, 'USD'),
            $escrow = factory(Escrow::class)->create()
        );

        $this->assertInstanceOf(StripeTransfer::class, $payout);
        $this->assertEquals($escrow->identifier, $payout->data['transfer_group']);
        $this->assertEquals(2500, $payout->data['amount']);
    }

    /** @test **/
    public function it_can_refund_a_charge()
    {
        $charge = $this->gateway()->charge($this->validCustomer(), new Amount(25));
        $refund = $this->gateway()->refund($charge);

        $this->assertInstanceOf(StripeRefund::class, $refund);
        $this->assertEquals(2500, $charge->retrieve()->amount_refunded);
    }

    /** @test **/
    public function it_can_refund_a_payout()
    {
        // put some funds
        $this->gateway()->charge($this->validCustomer(), new Amount(25, 'USD'));

        $payout = $this->gateway()->pay($this->validProvider(), new Amount(25, 'USD'));
        $refund = $this->gateway()->refund($payout);

        $this->assertInstanceOf(StripeTransferReversal::class, $refund);

        $this->assertEquals(2500, $payout->retrieve()->amount_reversed);
    }

    /**
     * @return StripePaymentGateway
     */
    protected function gateway()
    {
        return app(PaymentGatewayContract::class);
    }

    /**
     * @return Token
     */
    protected function validCard()
    {
        return Token::create([
            'card' => [
                'number' => '4000000000000077', // available instantly
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123',
            ],
        ]);
    }

    /**
     * @param Amount $amount
     *
     * @return StripeCharge
     */
    protected function validCharge(Amount $amount)
    {
        return StripeCharge::createFromObject(\Stripe\Charge::create([
            'amount' => $amount->toCents(),
            'currency' => $amount->currency()->getCode(),
            'customer' => $this->validCustomer()->stripeCustomer()->id,
        ]));
    }

    /**
     * @return Customer
     */
    protected function validCustomer()
    {
        return tap(factory(Customer::class)->create(), function (Customer $customer) {
            $customer->stripeCustomer()->store(\Stripe\Customer::create([
                'source' => $this->validCard(),
            ]));
        });
    }

    /**
     * @return Provider
     */
    protected function validProvider()
    {
        return tap(factory(Provider::class)->create(), function (Provider $customer) {
            $customer->stripeAccount()->store(\Stripe\Account::create([
                'type' => 'custom',
                'tos_acceptance' => [
                    'date' => time(),
                    'ip' => '127.0.0.1',
                ],
            ]));
        });
    }
}
