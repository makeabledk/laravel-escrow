<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowDeposited;
use Makeable\LaravelEscrow\Events\EscrowFunded;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Jobs\ChargeCustomer;

class DepositEscrow
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     */
    public function handle($escrow, $amount)
    {
        throw_unless(in_array($escrow->status->get(), ['open', 'committed']), IllegalEscrowAction::class);

        if ($amount->lte(Amount::zero())) {
            return;
        }

        // Insufficient funds on customer class
        if ($escrow->customer->getBalance()->lt($amount)) {
            ChargeCustomer::dispatch(
                $escrow->customer, $amount->subtract($escrow->customer->getBalance()), $escrow
            );
        }

        event(new EscrowDeposited($escrow, $escrow->deposit($amount, $escrow->customer)));

        if ($escrow->isFunded()) {
            event(new EscrowFunded($escrow));
        }
    }
}
