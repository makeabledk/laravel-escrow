<?php

namespace Makeable\LaravelEscrow\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract as PaymentGateway;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\CustomerCharged;

class ChargeCustomer
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $customer, $amount, $associatedEscrow = null;

    /**
     * @param CustomerContract $customer
     * @param Amount $amount
     * @param Escrow | null $associatedEscrow
     */
    public function __construct($customer, $amount, $associatedEscrow = null)
    {
        $this->customer = $customer;
        $this->amount = $amount;
        $this->associatedEscrow = $associatedEscrow;
    }

    /**
     */
    public function handle()
    {
        if ($this->amount->gt(Amount::zero())) {
            $charge = app(PaymentGateway::class)->charge($this->customer, $this->amount, $this->associatedEscrow);

            CustomerCharged::dispatch($this->customer, $this->customer->deposit($this->amount, $charge, $this->associatedEscrow));
        }
    }
}
