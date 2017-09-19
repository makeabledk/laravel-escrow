<?php

namespace Makeable\LaravelEscrow\Interactions;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Makeable\LaravelEscrow\Contracts\TransferContract as Charge;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowDeposited;
use Makeable\LaravelEscrow\Events\EscrowFunded;
use Makeable\LaravelEscrow\Transaction;

class CreateEscrowDeposit
{
    /**
     * @param Escrow      $escrow
     * @param Eloquent    $source
     * @param Charge|null $charge
     */
    public function handle($escrow, $source, $charge = null)
    {
        $transaction = tap(app(Transaction::class))
            ->setAmount($charge->getAmount())
            ->setTransfer($charge)
            ->setSource($source)
            ->setDestination($escrow)
            ->save();

        event(new EscrowDeposited($escrow, $transaction));

        if ($escrow->isFunded()) {
            event(new EscrowFunded($escrow));
        }
    }
}
