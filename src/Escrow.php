<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Contracts\EscrowRepositoryContract as EscrowRepository;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;
use Makeable\LaravelEscrow\Interactions\CancelEscrow;
use Makeable\LaravelEscrow\Interactions\ChargeCustomerDeposit;
use Makeable\LaravelEscrow\Interactions\Interact;
use Makeable\LaravelEscrow\Interactions\ReleaseEscrow;
use Makeable\ValueObjects\Amount\Amount;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Escrow extends Eloquent
{
    use Transactable;

    /**
     * @param EscrowableContract $escrowable
     * @param CustomerContract $customer
     * @param ProviderContract $provider
     *
     * @return Escrow
     */
    public static function init(...$args)
    {
        return app(EscrowRepository::class)->create(...$args);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function escrowable()
    {
        return $this->morphTo('escrowable');
    }

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
        return class_basename($this) . "#{$this->id}";
    }

    /**
     * @return mixed
     */
    public function isFunded()
    {
        return $this->getBalance()->gte($this->deposit_amount);
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
        if ($this->checkPolicy('cancel')) {
            Interact::call(CancelEscrow::class, $this);
        }

        return $this;
    }

    /**
     * @return Escrow
     */
    public function chargeDeposit()
    {
        if ($this->checkPolicy('deposit')) {
            Interact::call(ChargeCustomerDeposit::class, $this);
        }

        return $this;
    }

    /**
     * @return Escrow
     *
     * @throws InsufficientFunds
     * @throws IllegalEscrowAction
     */
    public function release()
    {
        if ($this->checkPolicy('release')) {
            Interact::call(ReleaseEscrow::class, $this);
        }

        return $this;
    }

    // _________________________________________________________________________________________________________________

    /**
     * @param $action
     * @param array $args
     *
     * @return bool
     */
    protected function checkPolicy($action, ...$args)
    {
        $policy = property_exists($this->escrowable, 'escrowPolicy')
            ? $this->escrowable->escrowPolicy
            : EscrowPolicy::class;

        return Interact::call($policy . '@' . $action, $this, ...$args);
    }
}
