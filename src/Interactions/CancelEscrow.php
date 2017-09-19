<?php

namespace Makeable\LaravelEscrow\Interactions;

use Carbon\Carbon;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowCancelled;

class CancelEscrow
{
    /**
     * @param Escrow $escrow
     */
    public function handle($escrow)
    {
        $escrow->policy()->check('cancel', $escrow);

        $escrow->deposits->each->refund();

        $escrow->cancelled_at = Carbon::now()->toDateTimeString();
        $escrow->save();

        event(new EscrowCancelled($escrow));
    }
}
