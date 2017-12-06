<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\SalesAccountDeposited;
use Makeable\LaravelEscrow\SalesAccount;

class DepositSalesAccount
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     */
    public function handle($escrow, $amount)
    {
        if (!$amount->equals(Amount::zero()) && app()->bound(SalesAccount::class)) {
            $transaction = $escrow->withdraw(
                $escrow->escrowable->getCustomerAmount()->subtract($escrow->escrowable->getProviderAmount()),
                $salesAccount = app(SalesAccount::class)
            );
            event(new SalesAccountDeposited($salesAccount, $transaction));
        }
    }
}
