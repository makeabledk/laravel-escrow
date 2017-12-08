<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowCancelled;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;

class CancelEscrow
{
    /**
     * @param Escrow          $escrow
     * @param bool | callable $refundDeposits
     */
    public function handle($escrow, $refundDeposits)
    {
        throw_unless(in_array($escrow->status->get(), ['open', 'committed']), IllegalEscrowAction::class);

        $escrow->deposits()->get()->each->reverse();

        if (true === $refundDeposits) {
            $escrow->customer->deposits()->associatedWith($escrow)->get()->each->attemptRefund('source');
        } elseif (is_callable($refundDeposits)) {
            call_user_func($refundDeposits, $escrow);
        }

        $escrow->cancelled_at = now();
        $escrow->save();

        event(new EscrowCancelled($escrow));
    }
}
