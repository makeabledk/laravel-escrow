<?php

namespace Makeable\LaravelEscrow;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\RefundableContract;

class Transaction extends Eloquent
{
    /**
     * @var string
     */
    public $table = 'escrow_transactions';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function associatedEscrow()
    {
        return $this->belongsTo(Escrow::class);
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

    // _________________________________________________________________________________________________________________

    /**
     * @param Builder      $query
     * @param Escrow | int $escrow
     *
     * @return mixed
     */
    public function scopeAssociatedWith($query, $escrow)
    {
        return $query->where('associated_escrow_id', is_object($escrow) ? $escrow->id : $escrow);
    }

    // _________________________________________________________________________________________________________________

    /**
     * @param $refundable
     *
     * @return bool
     *
     * @throws BadMethodCallException
     */
    public function attemptRefund($refundable)
    {
        if (!is_object($this->$refundable)) {
            throw new BadMethodCallException("Refundable '{$refundable}' is not an object");
        }

        if (in_array(RefundableContract::class, class_implements(get_class($this->$refundable)))) {
            $this->$refundable->refund();

            return true;
        }

        return false;
    }

    /**
     * @param Amount|null $amount
     *
     * @return Transaction
     */
    public function reverse(Amount $amount = null)
    {
        return tap((new static())
            ->setAmount($amount ?: $this->amount)
            ->setDestination($this->source)
            ->setSource($this->destination))
            ->setAssociatedEscrow($this->associated_escrow_id)
            ->save();
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
            'currency_code' => $amount->currency()->getCode(),
        ]);
    }

    /**
     * @param Escrow | int | null $escrow
     *
     * @return $this
     */
    public function setAssociatedEscrow($escrow)
    {
        return $this->fill([
            'associated_escrow_id' => is_object($escrow) ? $escrow->id : $escrow,
        ]);
    }

    /**
     * @param Eloquent $source
     *
     * @return $this
     */
    public function setDestination($source)
    {
        return $this->fill([
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
        return $this->fill([
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
        return new Amount($this->attributes['amount'], $this->currency);
    }
}
