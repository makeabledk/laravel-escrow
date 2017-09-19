<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelEscrow\Escrow;

interface EscrowRepositoryContract
{
    /**
     * @param EscrowableContract $escrowable
     * @param CustomerContract   $customer
     * @param ProviderContract   $provider
     *
     * @return Escrow
     */
    public function create(EscrowableContract $escrowable, CustomerContract $customer, ProviderContract $provider);
}
