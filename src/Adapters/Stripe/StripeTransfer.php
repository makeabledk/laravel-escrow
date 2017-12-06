<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Stripe\Transfer;

class StripeTransfer extends StripeObject
{
    /**
     * @param $id
     *
     * @return StripeTransfer
     */
    public static function findOrFail($id)
    {
        return new static(Transfer::retrieve($id));
    }

    /**
     * @return StripeTransfer
     */
    public function refund()
    {
        return new static($this->object->reverse());
    }
}
