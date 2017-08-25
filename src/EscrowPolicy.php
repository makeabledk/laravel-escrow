<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Contracts\TransactionContract;

abstract class EscrowPolicy
{
    /**
     * @param Escrow $escrow
     * @param EscrowableContract $escrowable
     *
     * @return bool
     */
    public function cancel($escrow, $escrowable)
    {
        $escrow->deposits->each(function($transaction) {
            $transaction->charge()->refund();
        });
        return true;
    }

    /**
     * @param Escrow $escrow
     * @param EscrowableContract $escrowable
     *
     * @return bool
     */
    public function cancelled($escrow, $escrowable)
    {
        return true;
    }

    /**
     * @param Escrow  $escrow
     * @param EscrowableContract  $escrowable
     * @param TransactionContract $transaction
     *
     * @return bool
     */
    public function deposit($escrow, $escrowable, $transaction)
    {
        return true;
    }

    /**
     * @param Escrow  $escrow
     * @param EscrowableContract  $escrowable
     * @param TransactionContract $transaction
     *
     * @return bool
     */
    public function deposited($escrow, $escrowable, $transaction)
    {
        return true;
    }

    /**
     * @param Escrow $escrow
     * @param EscrowableContract $escrowable
     *
     * @return bool
     */
    public function funded($escrow, $escrowable)
    {
        return true;
    }

    /**
     * @param Escrow $escrow
     * @param EscrowableContract $escrowable
     *
     * @return bool
     */
    public function release($escrow, $escrowable)
    {
        return true;
    }

    /**
     * @param Escrow $escrow
     * @param EscrowableContract $escrowable
     *
     * @return bool
     */
    public function released($escrow, $escrowable)
    {
        return true;
    }
}
