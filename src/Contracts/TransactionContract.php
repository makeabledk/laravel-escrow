<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\ValueObjects\Amount\Amount;

interface TransactionContract extends EloquentContract
{
    /**
     * @return Amount
     */
    public function getAmountAttribute();
}