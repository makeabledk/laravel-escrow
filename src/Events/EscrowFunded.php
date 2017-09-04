<?php

namespace Makeable\LaravelEscrow\Events;

use Illuminate\Queue\SerializesModels;
use Makeable\LaravelEscrow\Escrow;

class EscrowFunded
{
    use SerializesModels;

    /**
     * @var Escrow
     */
    protected $escrow;

    /**
     * @param Escrow $escrow
     */
    public function __construct($escrow)
    {
        $this->escrow = $escrow;
    }
}