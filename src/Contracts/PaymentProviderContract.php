<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\ValueObjects\Amount\Amount;

interface PaymentProviderContract
{
    /**
     * @param CustomerContract $customer
     * @param Amount $amount
     * @param $reference
     * @return ChargeContract
     */
    public function charge($customer, $amount, $reference = null);
}