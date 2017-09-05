<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Makeable\LaravelEscrow\Contracts\ChargeContract as Charge;
use Makeable\LaravelEscrow\Contracts\CustomerContract as Customer;
use Makeable\LaravelEscrow\Contracts\PaymentProviderContract as PaymentProvider;
use Makeable\LaravelEscrow\Contracts\ProviderContract as Provider;
use Makeable\LaravelEscrow\Contracts\TransferContract as Transfer;
use Makeable\ValueObjects\Amount\Amount;
use Stripe\Charge as StripeCharge;
use Stripe\Transfer as StripeTransfer;

class StripePaymentProvider implements PaymentProvider
{
    /**
     * @var mixed
     */
    protected $apiKey;

    /**
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param Customer $customer
     * @param Amount   $amount
     * @param null     $reference
     *
     * @return Charge
     */
    public function charge($customer, $amount, $reference = null)
    {
        $options = [
            'amount' => $amount->get(),
            'currency' => $amount->currency()->getCode(),
            'customer' => $customer->stripe_id,
            'api_key' => $this->apiKey,
        ];

        if ($reference) {
            $options['transfer_group'] = $reference;
        }

        return app()->make(Charge::class, [
            StripeCharge::create($options),
        ]);
    }

    /**
     * @param Provider $provider
     * @param Amount   $amount
     * @param null     $reference
     *
     * @return Transfer
     */
    public function pay($provider, $amount, $reference = null)
    {
        $options = [
            'amount' => $amount->get(),
            'currency' => $amount->currency()->getCode(),
            'destination' => $provider->stripe_account_id,
            'api_key' => $this->apiKey,
        ];

        if ($reference) {
            $options['transfer_group'] = $reference;
        }

        return app()->make(Transfer::class, [
            StripeTransfer::create($options),
        ]);
    }
}
