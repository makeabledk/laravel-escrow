<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\PaymentProviderContract as PaymentProvider;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowDeposited;
use Makeable\LaravelEscrow\Events\EscrowFunded;
use Makeable\LaravelEscrow\Events\ProviderDeposited;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Transfer;

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
