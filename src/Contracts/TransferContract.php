<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\ValueObjects\Amount\Amount;

interface TransferContract
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
     * @return TransferContract
     */
    public function refund();
}
