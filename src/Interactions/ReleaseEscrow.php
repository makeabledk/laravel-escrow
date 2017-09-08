<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Contracts\EscrowPolicyContract as EscrowPolicy;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowReleased;

class ReleaseEscrow
{
    /**
     * @param Escrow $escrow
     */
    public function handle($escrow)
    {
        app(EscrowPolicy::class)->check('release', $escrow);

        Interact::call(ChargeCustomerRemaining::class, $escrow);
        Interact::call(PayProvider::class, $escrow);

        $escrow->forceFill(['status' => 1])->save();

        event(new EscrowReleased($escrow));
    }
}
