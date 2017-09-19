<?php

namespace Makeable\LaravelEscrow\Interactions;

use Carbon\Carbon;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowReleased;

class CommitEscrow
{
    /**
     * @param Escrow $escrow
     */
    public function handle($escrow)
    {
        $escrow->policy()->check('commit', $escrow);

        Interact::call(ChargeEscrowDeposit::class, $escrow, $escrow->escrowable->getDepositAmount()->subtract($escrow->getBalance()));

        $escrow->committed_at = Carbon::now()->toDateTimeString();
        $escrow->save();

        event(new EscrowReleased($escrow));
    }
}
