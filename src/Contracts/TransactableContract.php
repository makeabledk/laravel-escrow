<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelCurrencies\Amount;

interface TransactableContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function deposits();

    /**
     * @return Amount
     */
    public function getBalance();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function withdrawals();
}
