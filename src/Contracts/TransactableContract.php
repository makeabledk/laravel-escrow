<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Transaction;

interface TransactableContract extends MorphableContract
{
    /**
     * @param Amount $amount
     * @param $source
     * @param Escrow | null $associatedEscrow
     *
     * @return Transaction
     */
    public function deposit($amount, $source, $associatedEscrow = null);

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
     * @param Escrow | null $associatedEscrow
     *
     * @return Transaction
     */
    public function withdraw($amount, $destination, $associatedEscrow = null);

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function withdrawals();
}
