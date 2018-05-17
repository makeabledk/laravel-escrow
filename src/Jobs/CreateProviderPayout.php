<?php

namespace Makeable\LaravelEscrow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract as PaymentGateway;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\ProviderPaid;
use Makeable\LaravelEscrow\Labels\AccountPayout;
use Makeable\LaravelEscrow\Labels\TransactionLabel;

class CreateProviderPayout
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $provider;
    public $amount;
    public $associatedEscrow;
    public $label;

    /**
     * @param ProviderContract $provider
     * @param Amount           $amount
     * @param Escrow | null    $associatedEscrow
     * @param TransactionLabel | string | null $label
     */
    public function __construct($provider, $amount, $associatedEscrow = null, $label = null)
    {
        $this->provider = $provider;
        $this->amount = $amount ?: $provider->getBalance();
        $this->associatedEscrow = $associatedEscrow;
        $this->label = $label;
    }

    public function handle()
    {
        if ($this->amount->gt(Amount::zero())) {
            $payout = app(PaymentGateway::class)->pay($this->provider, $this->amount, $this->associatedEscrow);

            ProviderPaid::dispatch($this->provider, $this->provider->withdraw($this->amount, $payout, function ($transaction) {
                $transaction->setLabel(app(AccountPayout::class));
            }));
        }
    }
}
