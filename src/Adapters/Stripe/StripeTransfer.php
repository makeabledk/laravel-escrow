<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\RefundableContract;
use Makeable\LaravelStripeObjects\StripeObject;

class StripeTransfer extends StripeObject implements RefundableContract
{
    /**
     * @param Amount|null $amount
     *
     * @return StripeTransfer
     */
    public function refund(Amount $amount = null)
    {
        return static::createFromObject(($object = $this->retrieve())->reverse([
            'amount' => $amount ? $amount->convertTo($object->currency)->get() * 100 : null,
        ]));
    }
}
