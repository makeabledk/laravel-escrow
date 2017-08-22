<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\TransactionContract as Transaction;
use Makeable\ValueObjects\Amount\Amount;

trait Depositable
{
    /**
     * @return mixed
     */
    public function charges()
    {
        return $this->morphMany(app(Transaction::class), 'source');
    }

    /**
     * @return mixed
     */
    public function deposits()
    {
        return $this->morphMany(app(Transaction::class), 'destination');
    }

    /**
     * @return Amount
     */
    public function getBalance()
    {
        return Amount::sum($this->deposits, 'amount')->subtract(Amount::sum($this->charges, 'amount'));
    }

    /**
     * @return Amount
     */
    public function getDepositAmountAttribute()
    {
        return new Amount($this->attributes['deposit_amount'], $this->deposit_currency);
    }

    /**
     * @return mixed
     */
    public function isFunded()
    {
        return $this->getBalance()->gte($this->deposit_amount);
    }
}
