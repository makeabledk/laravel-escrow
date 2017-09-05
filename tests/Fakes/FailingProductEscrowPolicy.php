<?php

namespace Makeable\LaravelEscrow\Tests\Fakes;

use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\EscrowPolicy;
use Makeable\LaravelEscrow\Transaction;

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
     * @return bool
     */
    public function deposit($escrowable)
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