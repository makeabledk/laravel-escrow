<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelEscrow\Escrow;

interface EscrowRepositoryContract
{
    /**
     * @param EscrowableContract $escrowable
     * @param CustomerContract   $escrowable
     * @param ProviderContract   $escrowable
     *
     * @return Escrow
     */
    public function create($escrowable, $customer, $provider);
}
