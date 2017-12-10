<?php

namespace Makeable\LaravelEscrow\Repositories;

use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Escrow;

class EscrowRepository
{
    /**
     * @param EscrowableContract $escrowable
     * @param CustomerContract   $customer
     * @param ProviderContract   $provider
     *
     * @return Escrow
     */
    public function findOrFail($escrowable, $customer = null, $provider = null)
    {
        return app(Escrow::class)->newQuery()
            ->escrowable($escrowable)
            ->when($customer, function ($query) use ($customer) {
                $query->customer($customer);
            })
            ->when($provider, function ($query) use ($provider) {
                $query->provider($provider);
            })
            ->firstOrFail();
    }

    /**
     * @param EscrowableContract $escrowable
     * @param CustomerContract   $customer
     * @param ProviderContract   $provider
     *
     * @return Escrow
     */
    public function create($escrowable, $customer, $provider)
    {
        return app(Escrow::class)->create([
            'escrowable_type' => $escrowable->getMorphClass(),
            'escrowable_id' => $escrowable->getKey(),
            'customer_type' => $customer->getMorphClass(),
            'customer_id' => $customer->getKey(),
            'provider_type' => $provider->getMorphClass(),
            'provider_id' => $provider->getKey(),
        ]);
    }
}
