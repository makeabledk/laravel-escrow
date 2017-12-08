<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelCurrencies\Amount;

interface RefundContract
{
    /**
     * @return Amount
     */
    public function getAmount();
}
