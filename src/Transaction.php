<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\TransactionContract;
use Makeable\ValueObjects\Amount\Amount;

class Transaction extends \Illuminate\Database\Eloquent\Model implements TransactionContract
{
    /**
     * @var string
     */
    protected $table = 'transactions';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return Amount
     */
    public function getAmountAttribute()
    {
        return new Amount($this->attributes['amount'], $this->currency);
    }
}
