<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelCurrencies\Amount;

interface EscrowableContract extends TransactableContract
{
    /**
     * @return Amount
     */
    public function getDepositAmount();

    /**
     * @return Amount
     */
    public function getCustomerAmount();

    /**
     * @return Amount
     */
    public function getProviderAmount();
}
