<?php

namespace Makeable\LaravelEscrow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract as PaymentGateway;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\CustomerCharged;
use Makeable\LaravelEscrow\Labels\AccountDeposit;
use Makeable\LaravelEscrow\Labels\TransactionLabel;

class ChargeCustomer
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $customer;
    public $amount;
    public $associatedEscrow;
    public $label;

    /**
     * @param CustomerContract $customer
     * @param Amount $amount
     * @param Escrow | null $associatedEscrow
     * @param TransactionLabel | string | null $label
     */
    public function __construct($customer, $amount, $associatedEscrow = null, $label = null)
    {
        $this->customer = $customer;
        $this->amount = $amount;
        $this->associatedEscrow = $associatedEscrow;
        $this->label = $label;
    }

    public function handle()
    {
        if ($this->amount->gt(Amount::zero())) {
            $charge = app(PaymentGateway::class)->charge($this->customer, $this->amount, $this->associatedEscrow);

            CustomerCharged::dispatch($this->customer, $this->customer->deposit($this->amount, $charge, function ($transaction) {
                $transaction->setAssociatedEscrow($this->associatedEscrow);
                $transaction->setLabel($this->label ?: app(AccountDeposit::class));
            }));
        }
    }
}
