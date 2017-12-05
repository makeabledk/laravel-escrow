<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\EscrowRepositoryContract;
use Makeable\LaravelEscrow\Contracts\ProviderContract;

trait Escrowable
{
    /**
     * @param CustomerContract $customer
     * @param ProviderContract $provider
     * @return Escrow
     */
    public function escrow($customer, $provider)
    {
        return app(Escrow::class)->newQuery()
                ->escrowable($this)
                ->customer($customer)
                ->provider($provider)
                ->first()
            ?: app(EscrowRepositoryContract::class)
                ->create($this, $customer, $provider);
    }
}
