<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowCancelled;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Labels\EscrowDepositRefund;

class CancelEscrow
{
    /**
     * @param Escrow $escrow
     * @param bool | callable $payoutRefundedDeposits
     * @param null $label
     */
    public function handle($escrow, $payoutRefundedDeposits, $label = null)
    {
        throw_unless(in_array($escrow->status->get(), ['open', 'committed']), IllegalEscrowAction::class);

        $escrow->deposits()->get()->each->reverse(function ($transaction) use ($label) {
            $transaction->setLabel($label ?: app(EscrowDepositRefund::class));
        });

        if (true === $payoutRefundedDeposits) {
            $escrow->customer->deposits()->associatedWith($escrow)->get()->each->attemptRefund('source');
        } elseif (is_callable($payoutRefundedDeposits)) {
            call_user_func($payoutRefundedDeposits, $escrow);
        }

        $escrow->cancelled_at = now();
        $escrow->save();

        event(new EscrowCancelled($escrow));
    }
}
