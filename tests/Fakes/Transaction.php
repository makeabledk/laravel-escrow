<?php

namespace Makeable\LaravelEscrow\Tests\Fakes;

use Makeable\LaravelEscrow\Contracts\TransactionContract;
use Makeable\ValueObjects\Amount\Amount;

class Transaction extends \Illuminate\Database\Eloquent\Model implements TransactionContract
{

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return Amount
     */
    public function getAmountAttribute()
    {
        return new Amount($this->attributes['amount'], $this->currency);
    }
}
