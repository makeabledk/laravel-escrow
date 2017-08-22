<?php

namespace Makeable\LaravelEscrow\Tests\Fakes;

use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Contracts\TransactionContract;
use Makeable\LaravelEscrow\EscrowPolicy;

class FailingProductEscrowPolicy extends EscrowPolicy
{
    /**
     * @param EscrowableContract $escrowable
     * @return bool
     */
    public function cancel($escrowable)
    {
        return false;
    }

    /**
     * @param EscrowableContract $escrowable
     * @param TransactionContract $transaction
     * @return bool
     */
    public function deposit($escrowable, $transaction)
    {
        return false;
    }

    /**
     * @param EscrowableContract $escrowable
     * @return bool
     */
    public function release($escrowable)
    {
        return false;
    }
}