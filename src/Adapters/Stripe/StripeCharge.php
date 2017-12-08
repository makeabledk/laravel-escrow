<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\RefundableContract;
use Makeable\LaravelStripeObjects\StripeObject;

class StripeCharge extends StripeObject implements RefundableContract
{
    /**
     * @param Amount|null $amount
     *
     * @return StripeCharge
     */
    public function refund(Amount $amount = null)
    {
        return static::createFromObject(($object = $this->retrieve())->refund([
            'amount' => $amount ? $amount->convertTo($object->currency)->get() * 100 : null,
        ]));
    }
}
