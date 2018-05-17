<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\SalesAccountContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\SalesAccountDeposited;
use Makeable\LaravelEscrow\Labels\PlatformFee;

class DepositSalesAccount
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     */
    public function handle($escrow, $amount)
    {
        if ($amount->toCents() !== 0 && app()->bound(SalesAccountContract::class)) {
            $transaction = $escrow->withdraw(
                $escrow->escrowable->getCustomerAmount()->subtract($escrow->escrowable->getProviderAmount()),
                $salesAccount = app(SalesAccountContract::class),
                function ($transaction) {
                    $transaction->setLabel(app(PlatformFee::class));
                }
            );
            event(new SalesAccountDeposited($salesAccount, $transaction));
        }
    }
}
