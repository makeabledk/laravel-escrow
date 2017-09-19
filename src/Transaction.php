<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Makeable\LaravelEscrow\Contracts\TransferContract;
use Makeable\ValueObjects\Amount\Amount;

class Transaction extends Eloquent
{
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
     * Refund the transfer and create a reversed transaction.
     *
     * @return Transaction
     */
    public function refund()
    {
        return tap(new static())
            ->setAmount($this->amount)
            ->setDestination($this->source)
            ->setSource($this->destination)
            ->setTransfer(optional($this->transfer)->refund())
            ->save();
    }

    // _________________________________________________________________________________________________________________

    /**
     * @return Amount
     */
    public function getAmountAttribute()
    {
        return new Amount($this->attributes['amount'], $this->currency);
    }

    /**
     * @return TransferContract
     */
    public function getTransferAttribute()
    {
        return $this->transfer_type ? call_user_func([$this->transfer_type, 'findOrFail'], $this->transfer_id) : null;
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

    /**
     * @param TransferContract $transfer
     *
     * @return $this
     */
    public function setTransfer($transfer)
    {
        return $transfer ? $this->forceFill([
            'transfer_type' => get_class($transfer),
            'transfer_id' => $transfer->getKey(),
        ]) : $this;
    }
}
