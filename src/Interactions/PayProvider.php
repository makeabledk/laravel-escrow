<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Contracts\PaymentProviderContract as PaymentProvider;
use Makeable\LaravelEscrow\Escrow;
use Makeable\ValueObjects\Amount\Amount;

class PayProvider
{
    /**
     * @param Escrow $escrow
     */
    public function handle($escrow, PaymentProvider $gateway)
    {
        $amount = $escrow->escrowable->getProviderAmount();

        if ($amount->gt(Amount::zero())) {
            $transfer = $gateway->pay($escrow->provider, $amount, $escrow->transfer_group);

            Interact::call(CreateEscrowWithdrawal::class, $escrow, $escrow->provider, $transfer);
        }
    }
}
