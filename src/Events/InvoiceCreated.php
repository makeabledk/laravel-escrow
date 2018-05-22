<?php

namespace Makeable\LaravelEscrow\Events;

use Illuminate\Queue\SerializesModels;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Invoice;
use Makeable\LaravelEscrow\Transaction;

class InvoiceCreated
{
    use SerializesModels;

    /**
     * @var Invoice
     */
    public $invoice;

    /**
     * @param Invoice $invoice
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }
}
