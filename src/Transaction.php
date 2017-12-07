<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\RefundableContract;

class Transaction extends Eloquent implements RefundableContract
{
    /**
     * @var array
     */
    protected $guarded = [];

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

    // _________________________________________________________________________________________________________________

    /**
     * @return Amount
     */
    public function getAmount()
    {
        return new Amount($this->attributes['amount'], $this->currency);
    }

    /**
     * Refund the transfer and create a reversed transaction.
     *
     * @return Transaction
     */
    public function refund()
    {
        if ($this->is_refund) {
            throw new \BadMethodCallException('Cannot refund an already refunded transaction');
        }

        return tap((new static())
            ->fill(['is_refund' => 1])
            ->setAmount($this->getAmount())
            ->setDestination($this->triggerRefund($this->source))
            ->setSource($this->triggerRefund($this->destination)))
            ->save();
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
            'currency_code' => $amount->currency()->getCode(),
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
     * @return Amount
     */
    public function getAmountAttribute()
    {
        return $this->getAmount();
    }

    /**
     * @param $refundable
     *
     * @return mixed
     */
    protected function triggerRefund($refundable)
    {
        $contracts = class_implements(get_class($refundable));

        return in_array(RefundableContract::class, $contracts)
            ? $refundable->refund()
            : $refundable;
    }
}
