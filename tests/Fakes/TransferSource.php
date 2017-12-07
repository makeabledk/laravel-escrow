<?php

namespace Makeable\LaravelEscrow\Tests\Fakes;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\RefundableContract;
use Makeable\LaravelEscrow\Contracts\TransferSourceContract;

class TransferSource implements TransferSourceContract
{
    /**
     * @return RefundableContract
     */
    public function refund()
    {
        return new static();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public static function findOrFail($id)
    {
        return new static();
    }

    /**
     * @return Amount
     */
    public function getAmount()
    {
        return new Amount(100);
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return rand();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [];
    }
}
