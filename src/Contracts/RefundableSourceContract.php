<?php

namespace Makeable\LaravelEscrow\Contracts;

interface RefundableContract
{
    /**
     * @return RefundableContract
     */
    public function refund();
}
