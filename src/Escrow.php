<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Contracts\EscrowRepositoryContract as EscrowRepository;
use Makeable\ValueObjects\Amount\Amount;

class Escrow extends \Illuminate\Database\Eloquent\Model
{
    use Transactable,
        EscrowActions;

    /**
     * @var string
     */
    protected $table = 'escrows';

    /**
     * @param EscrowableContract $escrowable
     * @return Escrow
     */
    public static function init($escrowable)
    {
        return app(EscrowRepository::class)->create($escrowable);
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
}
