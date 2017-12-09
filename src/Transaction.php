<?php

namespace Makeable\LaravelEscrow;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
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
    protected $casts = [
        'associated_escrow_id' => 'int',
    ];

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
     * @return Builder
     */
    public function scopeAssociatedWith($query, $escrow)
    {
        return $query->where('associated_escrow_id', is_object($escrow) ? $escrow->id : $escrow);
    }

    /**
     * @param Builder  $query
     * @param Eloquent $destination
     *
     * @return Builder
     */
    public function scopeDestinationIs($query, $destination)
    {
        return $query
            ->where('destination_type', $destination->getMorphClass())
            ->where('destination_id', $destination->getKey());
    }

    /**
     * @param Builder  $query
     * @param Eloquent $source
     *
     * @return Builder
     */
    public function scopeSourceIs($query, $source)
    {
        return $query
            ->where('source_type', $source->getMorphClass())
            ->where('source_id', $source->getKey());
    }

    /**
     * @param Builder $query
     * @param $object
     *
     * @return Builder mixed
     */
    public function scopeSourceOrDestinationIs($query, $object)
    {
        return $query->sourceIs($object)->orWhere(function ($query) use ($object) {
            $query->destinationIs($object);
        });
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

        if ($isRefundable = in_array(RefundableContract::class, class_implements(get_class($this->$refundable)))) {
            app(PaymentGatewayContract::class)->refund($this->$refundable);
        }

        return $isRefundable;
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
