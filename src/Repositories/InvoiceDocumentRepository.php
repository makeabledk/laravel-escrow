<?php

namespace Makeable\LaravelEscrow\Repositories;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Makeable\LaravelEscrow\Invoice;
use Spatie\Browsershot\Browsershot;

class InvoiceDocumentRepository
{
    /**
     * @param string $html
     * @param Invoice $invoice
     * @param null $filename
     * @return string
     * @throws \Exception
     */
    public function create($html, $invoice, $filename = null)
    {
        Browsershot::html($html)->savePdf($tempPdf = tempnam(sys_get_temp_dir(), 'EscrowInvoice').'.pdf');

        return tap($this->store(new File($tempPdf), $invoice, $filename), function () use ($tempPdf) {
            @unlink($tempPdf);
        });
    }

    /**
     * @param File $pdf
     * @param Invoice $invoice
     * @param null $filename
     * @return string
     * @throws \Exception
     */
    public function store($pdf, $invoice, $filename = null)
    {
        $filename = $filename ?: $invoice->id.'_'.$pdf->hashName();

        if (! Storage::disk($this->disk())->putFile($path = $this->path(), $pdf, $filename, [
            'visibility' => $this->visibility()
        ])) {
            throw new \Exception('Failed to store invoice');
        }

        $invoice->storage_path = $path.'/'.$filename;
        $invoice->save();

        return $this->getUrl($invoice);
    }

    /**
     * @param $invoice
     * @return string
     */
    public function getContents($invoice)
    {
        return Storage::disk($this->disk())->get($invoice->storage_path);
    }

    /**
     * @param $invoice
     * @return string
     */
    public function getUrl($invoice)
    {
        return Storage::disk($this->disk())->url($invoice->storage_path);
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function disk()
    {
        return config('laravel-escrow.invoice_storage.disk');
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function path()
    {
        return trim(config('laravel-escrow.invoice_storage.path'), '/');
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function visibility()
    {
        return config('laravel-escrow.invoice_storage.visibility');
    }
}
