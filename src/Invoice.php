<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Makeable\LaravelCurrencies\Amount;

class Invoice extends Eloquent
{
    /**
     * @var string
     */
    public $table = 'escrow_invoices';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function invoiceable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'escrow_invoice_id');
    }

    // _________________________________________________________________________________________________________________

    /**
     * @param $query
     * @param $invoiceable
     *
     * @return mixed
     */
    public function scopeInvoiceable($query, $invoiceable)
    {
        return $query
            ->where('invoiceable_type', $invoiceable->getMorphClass())
            ->where('invoiceable_id', $invoiceable->getKey());
    }

    // _________________________________________________________________________________________________________________

    /**
     * @return Amount
     */
    public function getTotalAmountAttribute()
    {
        return new Amount($this->attributes['total_amount'], $this->currency_code);
    }

    /**
     * @return Amount
     */
    public function getVatAmountAttribute()
    {
        return new Amount($this->attributes['vat_amount'], $this->currency_code);
    }
}
