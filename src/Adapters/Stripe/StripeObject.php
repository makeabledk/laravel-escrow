<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use BadMethodCallException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Makeable\LaravelEscrow\Contracts\TransferSourceContract;
use Makeable\LaravelCurrencies\Amount;
use Stripe\Charge;

abstract class StripeObject implements TransferSourceContract
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var Charge
     */
    protected $object;

    /**
     * @param \Stripe\StripeObject $object
     */
    public function __construct($object)
    {
        if (! $object instanceof \Stripe\StripeObject) {
            throw new ModelNotFoundException();
        }
        $this->id = $object->id;
        $this->object = $object;
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
        return new Amount($this->object->amount, $this->object->currency);
    }

    /**
     * @throws BadMethodCallException
     */
    public function refund()
    {
        throw new BadMethodCallException('Cant refund '.get_class($this));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->object->jsonSerialize();
    }
}
