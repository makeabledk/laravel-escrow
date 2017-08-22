<?php

namespace Makeable\LaravelEscrow\Contracts;

interface EloquentContract
{
    /**
     * @return mixed
     */
    public function getMorphClass();

    /**
     * @return mixed
     */
    public function getKey();
}