<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Makeable\LaravelEscrow\Contracts\ChargeContract;
use Makeable\ValueObjects\Amount\Amount;
use Stripe\Charge;

class StripeCharge implements ChargeContract
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
     ´    * @param Charge $object
     */
    public function __construct($object)
    {
        if (!$object instanceof Charge) {
            throw new ModelNotFoundException();
        }
        $this->id = $object->id;
        $this->object = $object;
    }

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
        // TODO handle refund amounts
        return new Amount($this->object->amount, $this->object->currency);
    }

    /**
     * @return StripeCharge
     */
    public function refund()
    {
        return new static($this->object->refund());
    }
}
