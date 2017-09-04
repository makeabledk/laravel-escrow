<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Contracts\PaymentProviderContract as PaymentProvider;
use Makeable\LaravelEscrow\Escrow;
use Makeable\ValueObjects\Amount\Amount;

class ChargeCustomerRemaining
{
    /**
     * @param Escrow $escrow
     * @param PaymentProvider $provider
     */
    public function handle($escrow, PaymentProvider $provider)
    {
        $amount = $escrow->escrowable->getDepositAmount()
            ->subtract($escrow->getBalance())
            ->minimum(Amount::zero());

        if ($amount->gt(Amount::zero())) {
            Interact::call(DepositToEscrow::class, $escrow, $provider->charge($escrow->customer, $amount));
        }
    }
}