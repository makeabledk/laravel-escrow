<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelCurrencies\Amount;

trait Transactable
{
    /**
     * @param Amount $amount
     * @param $source
     * @param Escrow | null $associatedEscrow
     *
     * @return Transaction
     */
    public function deposit($amount, $source, $associatedEscrow = null)
    {
        return tap(app(Transaction::class)
            ->setAmount($amount)
            ->setSource($source)
            ->setDestination($this)
            ->setAssociatedEscrow($associatedEscrow ?: $this->guessEscrowAssociation($source)))
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
        return Amount::sum($this->deposits()->get(), 'amount')->subtract(Amount::sum($this->withdrawals()->get(), 'amount'));
    }

    /**
     * @param Amount $amount
     * @param $destination
     * @param Escrow | null $associatedEscrow
     *
     * @return Transaction
     */
    public function withdraw($amount, $destination, $associatedEscrow = null)
    {
        return tap(app(Transaction::class)
            ->setAmount($amount)
            ->setSource($this)
            ->setDestination($destination)
            ->setAssociatedEscrow($associatedEscrow ?: $this->guessEscrowAssociation($destination)))
            ->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function withdrawals()
    {
        return $this->morphMany(app(Transaction::class), 'source');
    }

    /**
     * @param $other
     *
     * @return Escrow | null
     */
    private function guessEscrowAssociation($other)
    {
        if ($this instanceof Escrow) {
            return $this;
        } elseif ($other instanceof Escrow) {
            return $other;
        }
    }
}
