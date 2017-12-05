<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Contracts\PaymentProviderContract as PaymentProvider;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowWithdrawn;
use Makeable\LaravelEscrow\Transfer;
use Makeable\LaravelCurrencies\Amount;

class PayEscrowProvider
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     * @param PaymentProvider $gateway
     */
    public function handle($escrow, $amount, PaymentProvider $gateway)
    {
        if ($amount->gt(Amount::zero())) {
            $transaction = $escrow->withdraw($amount, tap((new Transfer)
                ->setAmount($amount)
                ->setSource($gateway->pay($escrow->provider, $amount, $escrow->transfer_group)))
                ->save()
            );

            event(new EscrowWithdrawn($escrow, $transaction));
        }
    }
}
