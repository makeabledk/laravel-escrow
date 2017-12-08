<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract as PaymentGateway;
use Makeable\LaravelEscrow\Events\CustomerCharged;

class ChargeCustomer
{
    /**
     * @param CustomerContract $customer
     * @param $amount
     * @param null $associatedEscrow
     */
    public function handle($customer, $amount, $associatedEscrow = null)
    {
        if ($amount->gt(Amount::zero())) {
            $charge = app(PaymentGateway::class)->charge($customer, $amount, $associatedEscrow);

            CustomerCharged::dispatch($customer, $customer->deposit($amount, $charge));
        }
    }
}
