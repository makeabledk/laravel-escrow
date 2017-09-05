<?php

namespace Makeable\LaravelEscrow\Interactions;

use Illuminate\Support\Collection;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowCancelled;

class CancelEscrow
{
    /**
     * @param Escrow $escrow
     */
    public function handle($escrow)
    {
        $escrow->deposits->each->refund();

        $escrow->forceFill(['status' => 0])->save();

        event(new EscrowCancelled($escrow));
    }
}
