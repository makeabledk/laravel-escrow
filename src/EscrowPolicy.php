<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Contracts\TransactionContract;

abstract class EscrowPolicy
{
    /**
     * @param EscrowableContract $escrowable
     * @return bool
     */
    public function cancel($escrowable)
    {
        return true;
    }

    /**
     * @param EscrowableContract $escrowable
     * @return bool
     */
    public function cancelled($escrowable)
    {
        return true;
    }

    /**
     * @param EscrowableContract $escrowable
     * @param TransactionContract $transaction
     * @return bool
     */
    public function deposit($escrowable, $transaction)
    {
        return true;
    }

    /**
     * @param EscrowableContract $escrowable
     * @param TransactionContract $transaction
     * @return bool
     */
    public function deposited($escrowable, $transaction)
    {
        return true;
    }

    /**
     * @param EscrowableContract $escrowable
     * @return bool
     */
    public function funded($escrowable)
    {
        return true;
    }

    /**
     * @param EscrowableContract $escrowable
     * @return bool
     */
    public function release($escrowable)
    {
        return true;
    }

    /**
     * @param EscrowableContract $escrowable
     * @return bool
     */
    public function released($escrowable)
    {
        return true;
    }
}