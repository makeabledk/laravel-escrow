<?php

namespace Makeable\LaravelEscrow\Repositories;

use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Contracts\EscrowRepositoryContract;
use Makeable\LaravelEscrow\Escrow;

class EscrowRepository implements EscrowRepositoryContract
{
    /**
     * @param EscrowableContract $escrowable
     *
     * @return Escrow
     */
    public function create($escrowable)
    {
        return app(Escrow::class)->forceCreate([
            'escrowable_type' => $escrowable->getMorphClass(),
            'escrowable_id' => $escrowable->getKey(),
            'deposit_amount' => ($deposit = $escrowable->getDepositAmount())->get(),
            'deposit_currency' => $deposit->currency()->getCode(),
            'status' => null,
        ]);
    }
}