<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\ProviderDeposited;
use Makeable\LaravelEscrow\Labels\ProviderPayment;
use Makeable\LaravelEscrow\Labels\TransactionLabel;

class DepositProvider
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     * @param TransactionLabel | string $label
     */
    public function handle($escrow, $amount, $label = null)
    {
        if ($amount->toCents() > 0) {
            ProviderDeposited::dispatch(
                $escrow->provider,
                $escrow->provider->deposit($amount, $escrow, function ($transaction) use ($label) {
                    $transaction->setLabel($label ?: app(ProviderPayment::class));
                })
            );
        }
    }
}
