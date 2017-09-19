<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Makeable\LaravelEscrow\Contracts\TransactionSourceContract;
use Makeable\LaravelEscrow\Contracts\TransferSourceContract;
use Makeable\ValueObjects\Amount\Amount;
use Stripe\Transfer;

class StripeTransfer implements TransferSourceContract
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var Transfer
     */
    protected $object;

    /**
     * @param Transfer $object
     */
    public function __construct($object)
    {
        if (!$object instanceof Transfer) {
            throw new ModelNotFoundException();
        }
        $this->id = $object->id;
        $this->object = $object;
    }

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
     * @return mixed
     */
    public function getKey()
    {
        return $this->id;
    }

    /**
     * @return Amount
     */
    public function getAmount()
    {
        // TODO handle (partial) refund amounts?

        return new Amount($this->object->amount, $this->object->currency);
    }

    /**
     * @return StripeTransfer
     */
    public function refund()
    {
        return new static($this->object->refund());
    }
}
