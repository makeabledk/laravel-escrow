<?php

namespace Makeable\LaravelEscrow;

use Makeable\ValueObjects\Amount\Amount;

trait Transactable
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function withdrawals()
    {
        return $this->morphMany(app(Transaction::class), 'source');
    }
}
