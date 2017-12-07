<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract as PaymentGateway;
use Makeable\LaravelEscrow\Events\CustomerCharged;
use Makeable\LaravelEscrow\Transfer;

class ChargeCustomer
{
    /**
     * @param CustomerContract $customer
     * @param $amount
     * @param null $reference
     */
    public function handle($customer, $amount, $reference = null)
    {
        if ($amount->gt(Amount::zero())) {
            $transaction = $customer->deposit($amount, tap((new Transfer())
                ->setAmount($amount)
                ->setSource(app(PaymentGateway::class)->charge($customer, $amount, $reference)))
                ->save()
            );

            event(new CustomerCharged($customer, $transaction));
        }
    }
}
