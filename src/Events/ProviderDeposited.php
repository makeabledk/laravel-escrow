<?php

namespace Makeable\LaravelEscrow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Transaction;

class ProviderDeposited
{
    use SerializesModels, Dispatchable;

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
     * @param Transaction      $transaction
     */
    public function __construct($provider, $transaction)
    {
        $this->provider = $provider;
        $this->transaction = $transaction;
    }
}
