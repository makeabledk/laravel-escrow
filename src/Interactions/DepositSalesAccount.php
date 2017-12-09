<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\SalesAccountContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\SalesAccountDeposited;

class DepositSalesAccount
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     */
    public function handle($escrow, $amount)
    {
        if (! $amount->equals(Amount::zero()) && app()->bound(SalesAccountContract::class)) {
            $transaction = $escrow->withdraw(
                $escrow->escrowable->getCustomerAmount()->subtract($escrow->escrowable->getProviderAmount()),
                $salesAccount = app(SalesAccountContract::class)
            );
            event(new SalesAccountDeposited($salesAccount, $transaction));
        }
    }
}
