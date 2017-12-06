<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\EscrowStatus;
use Makeable\LaravelEscrow\Events\EscrowReleased;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;

class CommitEscrow
{
    /**
     * @param Escrow $escrow
     */
    public function handle($escrow)
    {
        throw_unless($escrow->checkStatus(new EscrowStatus('open')), IllegalEscrowAction::class);

        Interact::call(DepositEscrow::class, $escrow, $escrow->escrowable->getDepositAmount()->subtract($escrow->getBalance()));

        $escrow->committed_at = now();
        $escrow->save();

        event(new EscrowReleased($escrow));
    }
}
