<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\EscrowStatus;
use Makeable\LaravelEscrow\Events\EscrowReleased;
use Makeable\LaravelEscrow\Events\SalesAccountDeposited;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\SalesAccount;

class ReleaseEscrow
{
    /**
     * @param Escrow $escrow
     */
    public function handle($escrow)
    {
        throw_unless($escrow->checkStatus(new EscrowStatus('committed')), IllegalEscrowAction::class);

        Interact::call(DepositEscrow::class, $escrow, $escrow->escrowable->getCustomerAmount()->subtract($escrow->getBalance()));
        Interact::call(DepositProvider::class, $escrow, $escrow->escrowable->getProviderAmount());
        Interact::call(DepositSalesAccount::class, $escrow, $escrow->escrowable->getCustomerAmount()->subtract($escrow->escrowable->getProviderAmount()));

        $escrow->released_at = now();
        $escrow->save();

        event(new EscrowReleased($escrow));
    }
}
