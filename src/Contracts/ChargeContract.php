<?php

namespace Makeable\LaravelEscrow\Contracts;

interface ChargeContract
{
    /**
     * @param $id
     * @return mixed
     */
    public static function findOrFail($id);

    /**
     * @return mixed
     */
    public function getKey();

    /**
     * @return ChargeContract
     */
    public function refund();
}