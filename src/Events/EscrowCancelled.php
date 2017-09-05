<?php

namespace Makeable\LaravelEscrow\Events;

use Illuminate\Queue\SerializesModels;
use Makeable\LaravelEscrow\Escrow;

class EscrowCancelled
{
    use SerializesModels;

    /**
     * @var Escrow
     */
    public $escrow;

    /**
     * @param Escrow $escrow
     */
    public function __construct($escrow)
    {
        $this->escrow = $escrow;
    }
}
