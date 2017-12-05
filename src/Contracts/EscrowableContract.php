<?php

namespace Makeable\LaravelEscrow\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Makeable\LaravelCurrencies\Amount;

interface EscrowableContract extends TransactableContract
{
    /**
     * @return BelongsTo
     */
    public function customer();

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

    /**
     * @return BelongsTo
     */
    public function provider();
}
