<?php

namespace Makeable\LaravelEscrow;

trait Escrowable
{
    /**
     * @return Escrow
     */
    public function escrow()
    {
        return app(Escrow::class)::escrowable($this)->first() ?: Escrow::init($this);
    }

    //
//    /**
//     * @return EscrowPolicy
//     *
//     * @throws \Exception
//     */
//    public function escrowPolicy()
//    {
//        if (!property_exists(static::class, 'escrowPolicy')) {
//            throw new \Exception('Missing escrow policy');
//        }
//
//        if (is_string(static::$escrowPolicy)) {
//            return new static::$escrowPolicy();
//        }
//
//        // For testing
//        return static::$escrowPolicy;
//    }
}
