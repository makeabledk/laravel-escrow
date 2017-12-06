<?php

namespace Makeable\LaravelEscrow\Events;

use Illuminate\Queue\SerializesModels;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Transaction;

class CustomerCharged
{
    use SerializesModels;

    /**
     * @var CustomerContract
     */
    public $customer;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * @param CustomerContract      $customer
     * @param Transaction $transaction
     */
    public function __construct($customer, $transaction)
    {
        $this->customer = $customer;
        $this->transaction = $transaction;
    }
}
