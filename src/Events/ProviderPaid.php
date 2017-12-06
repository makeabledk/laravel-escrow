<?php

namespace Makeable\LaravelEscrow\Events;

use Illuminate\Queue\SerializesModels;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Transaction;

class ProviderPaid
{
    use SerializesModels;

    /**
     * @var ProviderContract
     */
    public $provider;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * @param ProviderContract $provider
     * @param Transaction $transaction
     */
    public function __construct($provider, $transaction)
    {
        $this->provider = $provider;
        $this->transaction = $transaction;
    }
}
