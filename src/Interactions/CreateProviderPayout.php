<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\PaymentProviderContract as PaymentProvider;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowWithdrawn;
use Makeable\LaravelEscrow\Events\ProviderPaid;
use Makeable\LaravelEscrow\Transfer;

class CreateProviderPayout
{
    /**
     * @param ProviderContract          $provider
     * @param Amount          $amount
     * @param PaymentProvider $gateway
     */
    public function handle($provider, $amount = null, $reference = null, PaymentProvider $gateway)
    {
        $amount = $amount ?: $provider->getBalance();

        if ($amount->gt(Amount::zero())) {
            $transaction = $provider->withdraw($amount, tap((new Transfer)
                ->setAmount($amount)
                ->setSource($gateway->pay($provider, $amount, $reference)))
                ->save()
            );

            event(new ProviderPaid($provider, $transaction));
        }
    }
}
