<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Contracts\ChargeContract as Charge;
use Makeable\LaravelEscrow\Contracts\TransactionContract as Transaction;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowDeposited;
use Makeable\LaravelEscrow\Events\EscrowFunded;

class DepositToEscrow
{
    /**
     * @param Escrow $escrow
     * @param Charge $charge
     */
    public function handle($escrow, $charge)
    {
        tap(app(Transaction::class))
            ->setAmount($charge->getAmount())
            ->setCharge($charge)
            ->setSource($escrow->customer)
            ->setDestination($escrow)
            ->save();

        event(new EscrowDeposited($escrow));

        if($escrow->isFunded()) {
            event(new EscrowFunded($escrow));
        }
    }
}