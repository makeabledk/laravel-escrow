<?php

namespace Makeable\LaravelEscrow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Makeable\LaravelEscrow\Contracts\RefundableContract;
use Makeable\LaravelEscrow\Contracts\RefundContract;
use Makeable\LaravelEscrow\TransactionTypes\TransactionType;
use Makeable\LaravelEscrow\Transaction;

class CreateReversedTransaction
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $refundable;
    public $refund;
    public $transactionType;

    /**
     * @param RefundableContract $refundable
     * @param RefundContract $refund
     * @param TransactionType | string | null $transactionType
     */
    public function __construct($refundable, $refund, $transactionType = null)
    {
        $this->refundable = $refundable;
        $this->refund = $refund;
        $this->transactionType = $transactionType;
    }

    public function handle()
    {
        if ($this->refundAlreadyHandled()) {
            return;
        }

        Transaction::sourceIs($this->refundable)->get()->each(function (Transaction $original) {
            tap((new Transaction())
                ->setAmount($this->refund->getAmount())
                ->setSource($original->destination)
                ->setDestination($this->refund)
                ->setAssociatedEscrow($original->associated_escrow_id)
                ->setType($this->transactionType)
            )->save();
        });

        Transaction::destinationIs($this->refundable)->get()->each(function (Transaction $original) {
            tap((new Transaction())
                ->setAmount($this->refund->getAmount())
                ->setSource($this->refund)
                ->setDestination($original->source)
                ->setAssociatedEscrow($original->associated_escrow_id)
                ->setType($this->transactionType)
            )->save();
        });
    }

    /**
     * @return bool
     */
    protected function refundAlreadyHandled()
    {
        return Transaction::sourceOrDestinationIs($this->refund)->count() > 0;
    }
}
