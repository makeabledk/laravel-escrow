<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowDeposited;
use Makeable\LaravelEscrow\Events\EscrowFunded;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Jobs\ChargeCustomer;
use Makeable\LaravelEscrow\Labels\EscrowDeposit;
use Makeable\LaravelEscrow\Labels\TransactionLabel;

class DepositEscrow
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     * @param TransactionLabel | string $label
     * @throws \Throwable
     */
    public function handle($escrow, $amount, $label = null)
    {
        throw_unless(in_array($escrow->status->get(), ['open', 'committed']), IllegalEscrowAction::class);

        if ($amount->toCents() <= 0) {
            return;
        }

        // Insufficient funds on customer class
        if ($escrow->customer->getBalance()->lt($amount)) {
            ChargeCustomer::dispatch(
                $escrow->customer, $amount->subtract($escrow->customer->getBalance()), $escrow
            );
        }

        event(new EscrowDeposited($escrow, $escrow->deposit($amount, $escrow->customer, function ($transaction) use ($label) {
            $transaction->setLabel($label ?: app(EscrowDeposit::class));
        })));

        if ($escrow->isFunded()) {
            event(new EscrowFunded($escrow));
        }
    }
}
