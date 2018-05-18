<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowCancelled;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\TransactionTypes\EscrowDepositRefund;

class CancelEscrow
{
    /**
     * @param Escrow $escrow
     * @param bool | callable $payoutRefundedDeposits
     * @param null $transactionType
     */
    public function handle($escrow, $payoutRefundedDeposits, $transactionType = null)
    {
        throw_unless(in_array($escrow->status->get(), ['open', 'committed']), IllegalEscrowAction::class);

        $escrow->deposits()->get()->each->reverse(function ($transaction) use ($transactionType) {
            $transaction->setType($transactionType ?: app(EscrowDepositRefund::class));
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
