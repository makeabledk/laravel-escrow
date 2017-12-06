<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelCurrencies\Amount;

interface PaymentProviderContract
{
    /**
     * @param CustomerContract $customer
     * @param Amount           $amount
     * @param $reference
     *
     * @return TransferSourceContract
     */
    public function charge($customer, $amount, $reference = null);

    /**
     * @param ProviderContract $provider
     * @param Amount           $amount
     * @param $reference
     *
     * @return TransferSourceContract
     */
    public function pay($provider, $amount, $reference = null);
}
