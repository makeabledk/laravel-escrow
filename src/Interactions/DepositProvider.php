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
        if ($amount->gt(Amount::zero())) {
            event(new ProviderDeposited($escrow->provider, $escrow->provider->deposit($amount, $escrow)));
        }
    }
}
