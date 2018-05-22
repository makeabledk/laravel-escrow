<?php

namespace Makeable\LaravelEscrow\TransactionTypes;

class PlatformFee
{
    use TransactionType;

    /**
     * @return mixed
     */
    public function vatPercent()
    {
        return config('laravel-escrow.vat_percent');
    }
}
