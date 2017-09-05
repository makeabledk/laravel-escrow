<?php

namespace Makeable\LaravelEscrow\Contracts;

interface MorphableContract
{
    /**
     * @param $id
     *
     * @return mixed
     */
    public static function findOrFail($id);

    /**
     * @return mixed
     */
    public function getKey();
}
