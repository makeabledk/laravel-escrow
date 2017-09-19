<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Makeable\ValueObjects\Amount\Amount;

class Transaction extends Eloquent
{
    /**
     * Refund the transfer and create a reversed transaction.
     *
     * @return Transaction
     */
    public function refund()
    {
        return tap(new static)
            ->setAmount($this->getAmount())
            ->setDestination(tap($this->source)->refund()) // something needs to change here - could be either a transactable or charge
            ->setSource(tap($this->destination)->refund())
            ->save();
    }

    // _________________________________________________________________________________________________________________

    /**
     * @return Amount
     */
    public function getAmount()
    {
        return new Amount($this->attributes['amount'], $this->currency);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function destination()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function source()
    {
        return $this->morphTo();
    }

    /**
     * @param Amount $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        return $this->forceFill([
            'amount' => $amount->get(),
            'currency' => $amount->currency()->getCode(),
        ]);
    }

    /**
     * @param Eloquent $source
     *
     * @return $this
     */
    public function setDestination($source)
    {
        return $this->forceFill([
            'destination_type' => $source->getMorphClass(),
            'destination_id' => $source->getKey(),
        ]);
    }

    /**
     * @param Eloquent $source
     *
     * @return $this
     */
    public function setSource($source)
    {
        return $this->forceFill([
            'source_type' => $source->getMorphClass(),
            'source_id' => $source->getKey(),
        ]);
    }

    // _________________________________________________________________________________________________________________

    /**
     * @return Amount
     */
    public function getAmountAttribute()
    {
        return $this->getAmount();
    }
}
