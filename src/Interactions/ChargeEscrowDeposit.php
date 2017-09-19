<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelEscrow\Contracts\PaymentProviderContract as PaymentProvider;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowDeposited;
use Makeable\LaravelEscrow\Events\EscrowFunded;
use Makeable\LaravelEscrow\Transfer;
use Makeable\ValueObjects\Amount\Amount;

class ChargeEscrowDeposit
{
    /**
     * @param Escrow $escrow
     * @param $amount
     * @param PaymentProvider $gateway
     */
    public function handle($escrow, $amount, PaymentProvider $gateway)
    {
        if ($amount->gt(Amount::zero())) {
            $transaction = $escrow->deposit($amount, tap(new Transfer)
                ->setAmount($amount)
                ->setSource($gateway->charge($escrow->customer, $amount, $escrow->transfer_group))
                ->save()
            );

            event(new EscrowDeposited($escrow, $transaction));

            if ($escrow->isFunded()) {
                event(new EscrowFunded($escrow));
            }
        }
    }
}
