<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\ProviderDeposited;
use Makeable\LaravelEscrow\TransactionTypes\ProviderPayment;
use Makeable\LaravelEscrow\TransactionTypes\TransactionType;

class DepositProvider
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     * @param TransactionType | string $transactionType
     */
    public function handle($escrow, $amount, $transactionType = null)
    {
        if ($amount->toCents() > 0) {
            ProviderDeposited::dispatch(
                $escrow->provider,
                $escrow->provider->deposit($amount, $escrow, function ($transaction) use ($transactionType) {
                    $transaction->setType($transactionType ?: app(ProviderPayment::class));
                })
            );
        }
    }
}
