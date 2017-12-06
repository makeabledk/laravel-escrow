<?php

namespace Makeable\LaravelEscrow\Events;

use Illuminate\Queue\SerializesModels;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Transaction;

class SalesAccountDeposited
{
    use SerializesModels;

    /**
     * @var
     */
    public $salesAccount;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * @param $salesAccount
     * @param Transaction $transaction
     */
    public function __construct($salesAccount, $transaction)
    {
        $this->salesAccount = $salesAccount;
        $this->transaction = $transaction;
    }
}
