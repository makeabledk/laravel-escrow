<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\TransactionSourceContract;
use Makeable\ValueObjects\Amount\Amount;

trait Transactable
{
    /**
     * @param Amount $amount
     * @param TransactionSourceContract $source
     * @return Transaction
     */
    public function deposit($amount, $source)
    {
        return tap(app(Transaction::class))
            ->setAmount($amount)
            ->setSource($source)
            ->setDestination($this)
            ->save();
    }

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
     * @param Amount $amount
     * @param $destination
     * @return Transaction
     */
    public function withdraw($amount, $destination)
    {
        return tap(app(Transaction::class))
            ->setAmount($amount)
            ->setSource($this)
            ->setDestination($destination)
            ->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function withdrawals()
    {
        return $this->morphMany(app(Transaction::class), 'source');
    }
}
