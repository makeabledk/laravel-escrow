<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Contracts\EloquentContract;
use Makeable\LaravelEscrow\Contracts\TransactionContract as Transaction;
use Makeable\LaravelEscrow\Contracts\TransferContract as Transfer;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowWithdrawn;

class CreateEscrowWithdrawal
{
    /**
     * @param Escrow           $escrow
     * @param EloquentContract $destination
     * @param Transfer         $transfer
     */
    public function handle($escrow, $destination, $transfer)
    {
        $transaction = tap(app(Transaction::class))
            ->setAmount($transfer->getAmount())
            ->setCharge($transfer)
            ->setSource($escrow)
            ->setDestination($destination)
            ->save();

        event(new EscrowWithdrawn($escrow, $transaction));
    }
}
