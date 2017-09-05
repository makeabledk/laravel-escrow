<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowReleased;

class ReleaseEscrow
{
    /**
     * @param Escrow $escrow
     */
    public function handle($escrow)
    {
        Interact::call(ChargeCustomerRemaining::class, $escrow);
        Interact::call(PayProvider::class, $escrow);

        $escrow->forceFill(['status' => 1])->save();

        event(new EscrowReleased($escrow));
    }
}
