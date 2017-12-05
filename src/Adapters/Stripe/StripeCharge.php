<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Stripe\Charge;

class StripeCharge extends StripeObject
{
    /**
     * @param $id
     *
     * @return StripeCharge
     */
    public static function findOrFail($id)
    {
        return new static(Charge::retrieve($id));
    }

    /**
     * @return StripeCharge
     */
    public function refund()
    {
        return new static($this->object->refund());
    }
}
