<?php

namespace Makeable\LaravelEscrow\Events;

use Illuminate\Queue\SerializesModels;
use Makeable\LaravelEscrow\Invoice;

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
