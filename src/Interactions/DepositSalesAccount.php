<?php

namespace Makeable\LaravelEscrow\Interactions;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\PaymentProviderContract as PaymentProvider;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\EscrowDeposited;
use Makeable\LaravelEscrow\Events\EscrowFunded;
use Makeable\LaravelEscrow\Events\SalesAccountDeposited;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\SalesAccount;
use Makeable\LaravelEscrow\Transfer;

class DepositSalesAccount
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     */
    public function handle($escrow, $amount)
    {
        if (! $amount->equals(Amount::zero()) && app()->bound(SalesAccount::class)) {
            $transaction = $escrow->withdraw(
                $escrow->escrowable->getCustomerAmount()->subtract($escrow->escrowable->getProviderAmount()),
                $salesAccount = app(SalesAccount::class)
            );
            event(new SalesAccountDeposited($salesAccount, $transaction));
        }
    }
}
