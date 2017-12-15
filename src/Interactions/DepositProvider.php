<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\ProviderDeposited;

class DepositProvider
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     */
    public function handle($escrow, $amount)
    {
        if ($amount->toCents() > 0) {
            event(new ProviderDeposited($escrow->provider, $escrow->provider->deposit($amount, $escrow)));
        }
    }
}
