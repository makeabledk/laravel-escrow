<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Makeable\EloquentStatus\HasStatus;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Contracts\EscrowPolicyContract as EscrowPolicy;
use Makeable\LaravelEscrow\Contracts\EscrowRepositoryContract as EscrowRepository;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;
use Makeable\LaravelEscrow\Interactions\CancelEscrow;
use Makeable\LaravelEscrow\Interactions\ChargeCustomerDeposit;
use Makeable\LaravelEscrow\Interactions\CommitEscrow;
use Makeable\LaravelEscrow\Interactions\Interact;
use Makeable\LaravelEscrow\Interactions\ReleaseEscrow;
use Makeable\ValueObjects\Amount\Amount;

class Escrow extends Eloquent
{
    use HasStatus,
        Transactable;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function escrowable()
    {
        return $this->morphTo('escrowable');
    }

    /**
     * @return EscrowPolicy
     */
    public function policy()
    {
        return app(EscrowPolicy::class);
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
        return $this->getBalance()->gte($this->deposit_amount);
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
     * @return Amount
     */
    public function getDepositAmountAttribute()
    {
        return new Amount($this->attributes['deposit_amount'], $this->deposit_currency);
    }

    /**
     * @return string
     */
    public function getTransferGroupAttribute()
    {
        return class_basename($this)."#{$this->id}";
    }
}
