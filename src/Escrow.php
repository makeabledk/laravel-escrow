<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Makeable\EloquentStatus\HasStatus;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Contracts\TransactableContract;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;
use Makeable\LaravelEscrow\Interactions\CancelEscrow;
use Makeable\LaravelEscrow\Interactions\CommitEscrow;
use Makeable\LaravelEscrow\Interactions\Interact;
use Makeable\LaravelEscrow\Interactions\ReleaseEscrow;

class Escrow extends Eloquent implements TransactableContract
{
    use HasStatus,
        Transactable;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function customer()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function escrowable()
    {
        return $this->morphTo('escrowable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function provider()
    {
        return $this->morphTo();
    }

    // _________________________________________________________________________________________________________________

    /**
     * @param $query
     * @param CustomerContract $customer
     *
     * @return mixed
     */
    public function scopeCustomer($query, $customer)
    {
        return $query
            ->where('customer_type', $customer->getMorphClass())
            ->where('customer_id', $customer->getKey());
    }

    /**
     * @param $query
     * @param EscrowableContract $escrowable
     *
     * @return mixed
     */
    public function scopeEscrowable($query, $escrowable)
    {
        return $query
            ->where('escrowable_type', $escrowable->getMorphClass())
            ->where('escrowable_id', $escrowable->getKey());
    }

    /**
     * @param $query
     * @param ProviderContract $provider
     *
     * @return mixed
     */
    public function scopeProvider($query, $provider)
    {
        return $query
            ->where('provider_type', $provider->getMorphClass())
            ->where('provider_id', $provider->getKey());
    }

    // _________________________________________________________________________________________________________________

    /**
     * @return Escrow
     *
     * @throws IllegalEscrowAction
     */
    public function cancel()
    {
        Interact::call(CancelEscrow::class, $this);

        return $this;
    }

    /**
     * @return Escrow
     */
    public function commit()
    {
        Interact::call(CommitEscrow::class, $this);

        return $this;
    }

    /**
     * @return mixed
     */
    public function isFunded()
    {
        return $this->getBalance()->gte($this->escrowable->getDepositAmount());
    }

    /**
     * @return Escrow
     *
     * @throws InsufficientFunds
     * @throws IllegalEscrowAction
     */
    public function release()
    {
        Interact::call(ReleaseEscrow::class, $this);

        return $this;
    }

    // _________________________________________________________________________________________________________________

    /**
     * @return \Makeable\EloquentStatus\Status
     */
    public function getStatusAttribute()
    {
        return EscrowStatus::guess($this);
    }

    /**
     * @return string
     */
    public function getTransferGroupAttribute()
    {
        return class_basename($this)."#{$this->id}";
    }
}
