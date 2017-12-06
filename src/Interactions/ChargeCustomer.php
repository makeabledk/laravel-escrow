<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\PaymentProviderContract as PaymentProvider;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\CustomerCharged;
use Makeable\LaravelEscrow\Events\EscrowDeposited;
use Makeable\LaravelEscrow\Events\EscrowFunded;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Transfer;

class ChargeCustomer
{
    /**
     * @param CustomerContract $customer
     * @param $amount
     * @param null $reference
     * @param PaymentProvider $gateway
     */
    public function handle($customer, $amount, $reference = null, PaymentProvider $gateway)
    {
        if ($amount->gt(Amount::zero())) {
            $transaction = $customer->deposit($amount, tap((new Transfer)
                ->setAmount($amount)
                ->setSource($gateway->charge($customer, $amount, $reference)))
                ->save()
            );

            event(new CustomerCharged($customer, $transaction));
        }
    }
}
