<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelCurrencies\Amount;

interface TransferSourceContract extends RefundableContract
{
    /**
     * @param $id
     *
     * @return mixed
     */
    public static function findOrFail($id);

    /**
     * @return Amount
     */
    public function getAmount();

    /**
     * @return mixed
     */
    public function getKey();

    /**
     * @return array
     */
    public function toArray();
}
