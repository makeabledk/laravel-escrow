<?php

namespace Makeable\LaravelEscrow\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Makeable\LaravelEscrow\EscrowPolicy;
use Makeable\ValueObjects\Amount\Amount;

interface EscrowableContract extends EloquentContract
{
    /**
     * @return BelongsTo
     */
    public function customer();

    /**
     * @return EscrowPolicy
     */
    public function escrowPolicy();

    /**
     * @return Amount
     */
    public function getDepositAmount();

    /**
     * @return Amount
     */
    public function getFullAmount();

    /**
     * @return BelongsTo
     */
    public function provider();
}
