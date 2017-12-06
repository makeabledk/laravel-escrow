<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Transaction;

interface TransactableContract
{
    /**
     * @param Amount $amount
     * @param $source
     *
     * @return Transaction
     */
    public function deposit($amount, $source);

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function deposits();

    /**
     * @return Amount
     */
    public function getBalance();

    /**
     * @param Amount $amount
     * @param $destination
     *
     * @return Transaction
     */
    public function withdraw($amount, $destination);

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function withdrawals();
}
