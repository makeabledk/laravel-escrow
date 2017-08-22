<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelEscrow\EscrowPolicy;
use Makeable\ValueObjects\Amount\Amount;

interface EscrowableContract extends EloquentContract
{
    /**
     * @return Amount
     */
    public function getDepositAmount();

    /**
     * @return EscrowPolicy
     */
    public function escrowPolicy();
}