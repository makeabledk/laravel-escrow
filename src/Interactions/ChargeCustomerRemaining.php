<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Contracts\PaymentProviderContract as PaymentProvider;
use Makeable\LaravelEscrow\Escrow;
use Makeable\ValueObjects\Amount\Amount;

class ChargeCustomerRemaining
{
    /**
     * @param Escrow          $escrow
     * @param PaymentProvider $provider
     */
    public function handle($escrow, PaymentProvider $provider)
    {
        $amount = $escrow->escrowable->getCustomerAmount()->subtract($escrow->getBalance());

        if ($amount->gt(Amount::zero())) {
            Interact::call(CreateEscrowDeposit::class, $escrow, $escrow->customer, $provider->charge($escrow->customer, $amount, $escrow->transfer_group));
        }
    }
}
