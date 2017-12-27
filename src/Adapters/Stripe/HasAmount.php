<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Makeable\LaravelCurrencies\Amount;

trait HasAmount
{
    /**
     * @return Amount
     */
    public function getAmount()
    {
        return Amount::fromCents(
            data_get($this->data, 'amount'),
            data_get($this->data, 'currency')
        );
    }
}
