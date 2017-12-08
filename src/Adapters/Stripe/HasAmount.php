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
        return new Amount($this->amount / 100, $this->currency);
    }
}