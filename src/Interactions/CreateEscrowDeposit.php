<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Contracts\ChargeContract as Charge;
use Makeable\LaravelEscrow\Contracts\EloquentContract;
use Makeable\LaravelEscrow\Contracts\TransactionContract as Transaction;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowDeposited;
use Makeable\LaravelEscrow\Events\EscrowFunded;

class CreateEscrowDeposit
{
    /**
     * @param Escrow           $escrow
     * @param EloquentContract $source
     * @param Charge           $charge
     */
    public function handle($escrow, $source, $charge)
    {
        $transaction = tap(app(Transaction::class))
            ->setAmount($charge->getAmount())
            ->setCharge($charge)
            ->setSource($source)
            ->setDestination($escrow)
            ->save();

        event(new EscrowDeposited($escrow, $transaction));

        if ($escrow->isFunded()) {
            event(new EscrowFunded($escrow));
        }
    }
}
