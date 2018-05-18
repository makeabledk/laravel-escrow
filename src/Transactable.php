<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Database\Eloquent\Builder;
use Makeable\LaravelCurrencies\Amount;

trait Transactable
{
    /**
     * @param Amount $amount
     * @param $source
     * @param callable $callable
     *
     * @return Transaction
     */
    public function deposit($amount, $source, $callable = null)
    {
        return tap(app(Transaction::class)
            ->setAmount($amount)
            ->setSource($source)
            ->setDestination($this)
            ->setAssociatedEscrow($this->guessEscrowAssociation($source))
            ->pipe($callable))
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
     * @return Builder
     */
    public function transactions()
    {
        return app(Transaction::class)->newQuery()->sourceOrDestinationIs($this);
    }

    /**
     * @param Amount $amount
     * @param $destination
     * @param callable $callable
     *
     * @return Transaction
     */
    public function withdraw($amount, $destination, $callable = null)
    {
        return tap(app(Transaction::class)
            ->setAmount($amount)
            ->setSource($this)
            ->setDestination($destination)
            ->setAssociatedEscrow($this->guessEscrowAssociation($destination))
            ->pipe($callable))
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
