<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\TransactionContract as Transaction;
use Makeable\ValueObjects\Amount\Amount;

trait Transactable
{
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
        return Amount::sum($this->deposits, 'amount')->subtract(Amount::sum($this->withdrawals, 'amount'));
    }

    /**
     * @return mixed
     */
    public function withdrawals()
    {
        return $this->morphMany(app(Transaction::class), 'source');
    }
}
