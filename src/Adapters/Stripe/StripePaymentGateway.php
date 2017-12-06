<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Stripe\Charge;
use Stripe\Transfer;

class StripePaymentGateway implements PaymentGatewayContract
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
     * @param CustomerContract $customer
     * @param Amount           $amount
     * @param null             $reference
     *
     * @return StripeCharge
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

        return app()->make(StripeCharge::class, [Charge::create($options)]);
    }

    /**
     * @param ProviderContract $provider
     * @param Amount           $amount
     * @param null             $reference
     *
     * @return StripeTransfer
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

        return app()->make(StripeTransfer::class, [Transfer::create($options)]);
    }
}
