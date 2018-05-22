<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\SalesAccountContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\SalesAccountDeposited;
use Makeable\LaravelEscrow\TransactionTypes\PlatformFee;

class DepositSalesAccount
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     * @param null $transactionType
     */
    public function handle($escrow, $amount, $transactionType = null)
    {
        if ($amount->toCents() !== 0 && app()->bound(SalesAccountContract::class)) {
            $transaction = $escrow->withdraw(
                $amount,
                $salesAccount = app(SalesAccountContract::class),
                function ($transaction) use ($transactionType) {
                    $transaction->setType($transactionType ?: app(PlatformFee::class));
                }
            );
            event(new SalesAccountDeposited($salesAccount, $transaction));
        }
    }
}
