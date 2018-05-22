<?php

namespace Makeable\LaravelEscrow\Tests\Feature\Interactions;

use Makeable\LaravelEscrow\Jobs\CreateInvoice;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Tests\FakePaymentGateway;

class InvoiceTest extends DatabaseTestCase
{
    use FakePaymentGateway;

    /** @test **/
    public function an_invoice_can_be_created_for_given_transactions()
    {
        $this->escrow->commit()->release();

        CreateInvoice::dispatch(
//            $this->customer, $this->escrow->
        );
    }
}