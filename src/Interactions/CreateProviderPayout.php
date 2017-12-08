<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract as PaymentGateway;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\ProviderPaid;

class CreateProviderPayout
{
    /**
     * @param ProviderContract $provider
     * @param Amount           $amount
     * @param Escrow | null    $associatedEscrow
     */
    public function handle($provider, $amount = null, $associatedEscrow = null)
    {
        $amount = $amount ?: $provider->getBalance();

        if ($amount->gt(Amount::zero())) {
            $payout = app(PaymentGateway::class)->pay($provider, $amount, $associatedEscrow);

            ProviderPaid::dispatch($provider, $provider->withdraw($amount, $payout));
        }
    }
}
