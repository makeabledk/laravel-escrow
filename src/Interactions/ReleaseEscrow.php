<?php

namespace Makeable\LaravelEscrow\Interactions;

use Carbon\Carbon;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowReleased;

class ReleaseEscrow
{
    /**
     * @param Escrow $escrow
     */
    public function handle($escrow)
    {
        $escrow->policy()->check('release', $escrow);

        Interact::call(ChargeEscrowDeposit::class, $escrow, $escrow->escrowable->getCustomerAmount()->subtract($escrow->getBalance()));
        Interact::call(PayEscrowProvider::class, $escrow, $escrow->escrowable->getProviderAmount());

        $escrow->released_at = Carbon::now()->toDateTimeString();
        $escrow->save();

        event(new EscrowReleased($escrow));
    }
}
