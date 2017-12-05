<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Makeable\LaravelEscrow\Contracts\RefundableContract;
use Makeable\LaravelEscrow\Contracts\TransactionSourceContract;
use Makeable\LaravelEscrow\Contracts\TransferSourceContract;
use Makeable\LaravelCurrencies\Amount;

class Transfer extends Eloquent implements RefundableContract
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = [
        'source_data' => 'array'
    ];

    /**
     * Refund the transfer and create a reversed transaction.
     *
     * @return Transaction
     */
    public function refund()
    {
        if ($this->is_refund) {
            throw new \BadMethodCallException('Cannot perform refund on a refunded transfer');
        }

        return tap(new static)
            ->fill(['is_refund' => 1])
            ->setAmount($this->getAmount())
            ->setSource($this->source->refund())
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
     * @param Amount $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        return $this->fill([
            'amount' => $amount->get(),
            'currency' => $amount->currency()->getCode(),
        ]);
    }

    /**
     * @param TransferSourceContract $transfer
     *
     * @return $this
     */
    public function setSource(TransferSourceContract $source)
    {
        return $this->fill([
            'transfer_type' => get_class($source),
            'transfer_id' => $source->getKey(),
            'transfer_data' => $source->toArray()
        ]);
    }

    // _________________________________________________________________________________________________________________

    /**
     * @return RefundableContract
     */
    public function getSourceAttribute()
    {
        return $this->source_type ? call_user_func([$this->source_type, 'findOrFail'], $this->source_id) : null;
    }
}
