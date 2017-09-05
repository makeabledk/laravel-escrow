<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;

class EscrowPolicy
{
    /**
     * @param Escrow $escrow
     *
     * @return bool
     */
    public function cancel($escrow)
    {
        throw_if($escrow->status !== null, IllegalEscrowAction::class);
        throw_if($escrow->withdrawals()->count(), IllegalEscrowAction::class, 'Cannot cancel escrow that has withdrawals');

        return true;
    }

    /**
     * @param Escrow $escrow
     *
     * @return bool
     */
    public function deposit($escrow)
    {
        throw_if($escrow->status !== null, IllegalEscrowAction::class);

        return true;
    }

    /**
     * @param Escrow $escrow
     *
     * @return bool
     */
    public function release($escrow)
    {
        throw_if($escrow->status !== null, IllegalEscrowAction::class);
        throw_unless($escrow->isFunded(), InsufficientFunds::class);

        return true;
    }
}
