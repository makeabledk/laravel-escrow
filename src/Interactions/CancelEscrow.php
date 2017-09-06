<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Contracts\EscrowPolicyContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowCancelled;

class CancelEscrow
{
    /**
     * @param Escrow $escrow
     */
    public function handle($escrow)
    {
        app(EscrowPolicyContract::class)->check('cancel', $escrow);

        $escrow->deposits->each->refund();

        $escrow->forceFill(['status' => 0])->save();

        event(new EscrowCancelled($escrow));
    }
}
