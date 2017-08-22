<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\EscrowableContract;

class Escrow extends \Illuminate\Database\Eloquent\Model
{
    use Depositable,
        EscrowActions;

    /**
     * @param EscrowableContract $escrowable
     * @return Escrow
     */
    public static function init($escrowable)
    {
        return static::forceCreate([
            'escrowable_type' => $escrowable->getMorphClass(),
            'escrowable_id' => $escrowable->getKey(),
            'deposit_amount' => ($deposit = $escrowable->getDepositAmount())->get(),
            'deposit_currency' => $deposit->currency()->getCode(),
            'status' => null
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function escrowable()
    {
        return $this->morphTo('escrowable');
    }

    /**
     * @param $query
     * @param EscrowableContract $escrowable
     * @return mixed
     */
    public function scopeEscrowable($query, $escrowable)
    {
        return $query
            ->where('escrowable_type', $escrowable->getMorphClass())
            ->where('escrowable_id', $escrowable->getKey());
    }
}