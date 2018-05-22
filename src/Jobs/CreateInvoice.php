<?php

namespace Makeable\LaravelEscrow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\MorphableContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Invoice;
use Makeable\LaravelEscrow\Repositories\InvoiceDocumentRepository;

class CreateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var MorphableContract
     */
    public $invoiceable;

    /**
     * @var Collection
     */
    public $transactions;

    /**
     * @var string
     */
    public $view;

    /**
     * @var string
     */
    public $line1;

    /**
     * @var string
     */
    public $line2;

    /**
     * @param MorphableContract $invoiceable
     * @param Collection $transactions
     * @param string $view
     * @param null $line1
     * @param null $line2
     */
    public function __construct($invoiceable, $transactions, $view = 'laravel-escrow::invoice', $line1 = null, $line2 = null)
    {
        $this->invoiceable = $invoiceable;
        $this->transactions = $transactions;
        $this->view = $view;
        $this->line1 = $line1;
        $this->line2 = $line2;
    }

    public function handle()
    {
        DB::transaction(function () {
            $invoice = $this->createInvoice();

            app(InvoiceDocumentRepository::class)->create(view($this->view, [
                'invoice' => $invoice,
                'transactions' => $this->transactions,
            ]), $invoice);

            $invoice->transactions()->saveMany($this->transactions);
        });
    }

    /**
     * @return Invoice
     * @throws \Exception
     */
    protected function createInvoice()
    {
        $invoice = app(Invoice::class);
        $invoice->line1 = $this->line1;
        $invoice->line2 = $this->line2;
        $invoice->total_amount = Amount::sum($this->transactions, 'amount');
        $invoice->vat_amount = Amount::sum($this->transactions, 'vat_amount');
        $invoice->save();

        return $invoice;
    }
}
