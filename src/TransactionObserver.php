<?php

namespace Makeable\LaravelEscrow;

class TransactionObserver
{
    /**
     * Add description when saving transaction.
     *
     * @param Transaction $transaction
     */
    public function saving($transaction)
    {
        $transaction->vat_percent = $vatPercent = optional($transaction->type())->vatPercent();
        $transaction->vat_amount = is_null($vatPercent) ? null : $transaction->amount->multiply($vatPercent);
    }
}
