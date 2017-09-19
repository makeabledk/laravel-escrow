<?php

namespace Makeable\LaravelEscrow\Interactions;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Makeable\LaravelEscrow\Contracts\TransferContract as Transfer;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowWithdrawn;
use Makeable\LaravelEscrow\Transaction;

class CreateEscrowWithdrawal
{
    /**
     * @param Escrow        $escrow
     * @param Eloquent      $destination
     * @param Transfer|null $transfer
     */
    public function handle($escrow, $destination, $transfer = null)
    {
        $transaction = tap(app(Transaction::class))
            ->setAmount($transfer->getAmount())
            ->setTransfer($transfer)
            ->setSource($escrow)
            ->setDestination($destination)
            ->save();

        event(new EscrowWithdrawn($escrow, $transaction));
    }
}
