<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\ValueObjects\Amount\Amount;

interface TransferContract extends MorphableContract
{
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
