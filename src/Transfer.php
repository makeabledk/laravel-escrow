<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Makeable\LaravelEscrow\Contracts\TransferSourceContract;
use Makeable\ValueObjects\Amount\Amount;

class Transfer extends Eloquent
{
    /**
     * Refund the transfer and create a reversed transaction.
     *
     * @return Transaction
     */
    public function refund()
    {
        return tap(new static)
            ->setAmount($this->amount)
            ->setSource($this->source->refund())
            ->save();
    }

    // _________________________________________________________________________________________________________________

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
     * @param TransferSourceContract $transfer
     *
     * @return $this
     */
    public function setSource($transfer)
    {
        return $transfer ? $this->forceFill([
            'transfer_type' => get_class($transfer),
            'transfer_id' => $transfer->getKey(),
        ]) : $this;
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
     * @return TransferSourceContract
     */
    public function getSourceAttribute()
    {
        return $this->source_type ? call_user_func([$this->source_type, 'findOrFail'], $this->source_id) : null;
    }
}
