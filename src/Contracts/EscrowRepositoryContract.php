<?php

namespace Makeable\LaravelEscrow\Contracts;

interface EscrowRepositoryContract
{

    public function create($escrowable);

}