<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Makeable\LaravelEscrow\Contracts\RefundableContract;
use Makeable\LaravelEscrow\Contracts\TransferSourceContract;
use Makeable\LaravelCurrencies\Amount;
use Stripe\Transfer;
use Stripe\TransferReversal;

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
