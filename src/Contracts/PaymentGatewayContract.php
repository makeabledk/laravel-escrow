<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Escrow;

interface PaymentGatewayContract
{
    /**
     * @param CustomerContract $customer
     * @param Amount           $amount
     * @param Escrow | null    $associatedEscrow
     *
     * @return TransferSourceContract
     */
    public function charge($customer, $amount, $associatedEscrow = null);

    /**
     * @param ProviderContract $provider
     * @param Amount           $amount
     * @param Escrow | null    $associatedEscrow
     *
     * @return TransferSourceContract
     */
    public function pay($provider, $amount, $associatedEscrow = null);
}
