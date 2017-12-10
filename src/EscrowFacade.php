<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Support\Facades\Facade;
use Makeable\LaravelEscrow\Repositories\EscrowRepository;

class EscrowFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return EscrowRepository::class;
    }
}
