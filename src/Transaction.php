<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\ChargeContract;
use Makeable\LaravelEscrow\Contracts\EloquentContract;
use Makeable\LaravelEscrow\Contracts\TransactionContract;
use Makeable\ValueObjects\Amount\Amount;

class Transaction extends \Illuminate\Database\Eloquent\Model implements TransactionContract
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
     * Refund the charge and create a reversed transaction
     *
     * @return Transaction
     */
    public function refund()
    {
        return tap(new static)
            ->setAmount($this->amount)
            ->setCharge($this->charge->refund())
            ->setDestination($this->source)
            ->setSource($this->destination)
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
     * @return ChargeContract
     */
    public function getChargeAttribute()
    {
        return app()->call([ChargeContract::class, 'findOrFail'], [$this->charge_id]);
    }

    /**
     * @param Amount $amount
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
     * @param ChargeContract $charge
     * @return $this
     */
    public function setCharge($charge)
    {
        return $this->forceFill([
            'charge_id' => $charge->getKey(),
        ]);
    }

    /**
     * @param EloquentContract $source
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
     * @param EloquentContract $source
     * @return $this
     */
    public function setSource($source)
    {
        return $this->forceFill([
            'source_type' => $source->getMorphClass(),
            'source_id' => $source->getKey(),
        ]);
    }
}
