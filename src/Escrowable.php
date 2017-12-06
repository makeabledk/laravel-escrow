<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Repositories\EscrowRepository;
use Makeable\LaravelEscrow\Contracts\ProviderContract;

trait Escrowable
{
    use Transactable;

    /**
     * @param CustomerContract $customer
     * @param ProviderContract $provider
     *
     * @return Escrow
     */
    public function escrow($customer, $provider)
    {
        return app(EscrowRepository::class)->find($this, $customer, $provider)
            ?: app(EscrowRepository::class)->create($this, $customer, $provider);
    }
}
