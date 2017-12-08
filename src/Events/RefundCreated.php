<?php

namespace Makeable\LaravelEscrow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\RefundableContract;
use Makeable\LaravelEscrow\Contracts\RefundContract;
use Makeable\LaravelEscrow\Transaction;

class RefundCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @var RefundContract
     */
    public $refund;

    /**
     * @var RefundableContract
     */
    public $refundable;

    /**
     * @param RefundableContract $refundable
     * @param RefundContract $refund
     */
    public function __construct($refund, $refundable)
    {
        $this->refund = $refund;
        $this->refundable = $refundable;
    }
}
