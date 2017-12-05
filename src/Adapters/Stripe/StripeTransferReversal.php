<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

class StripeTransferReversal extends StripeObject
{
    /**
     * @param $id
     *
     * @return StripeTransferReversal
     */
    public static function findOrFail($id)
    {
        return new static(null);
    }
}
