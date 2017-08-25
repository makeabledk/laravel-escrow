<?php

namespace Makeable\StripeConnectEscrow\Stripe;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Makeable\LaravelEscrow\Contracts\ChargeContract;
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
     * StripeCharge constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param $id
     * @return StripeCharge
     */
    public static function findOrFail($id)
    {
        $charge = new static;
        $charge->id = $id;
        $charge->object = Charge::retrieve($id);

        if(!$charge->object) {
            throw new ModelNotFoundException();
        }

        return $charge;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->id;
    }

    /**
     * @return StripeCharge
     */
    public function refund()
    {
        $refund = new static;
        $refund->object = $this->object->refund();
        $refund->id = $refund->object['id'];

        return $refund;
    }
}