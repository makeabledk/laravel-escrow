<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Makeable\LaravelEscrow\Contracts\ChargeContract as Charge;
use Makeable\LaravelEscrow\Contracts\CustomerContract as Customer;
use Makeable\LaravelEscrow\Contracts\PaymentProviderContract;
use Makeable\ValueObjects\Amount\Amount;
use Stripe\Charge as StripeCharge;

class StripePaymentProvider implements PaymentProviderContract
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
     * @param Amount $amount
     * @return Charge
     */
    public function charge($customer, $amount)
    {
        $charge = StripeCharge::create([
            'amount' => $amount->get(),
            'currency' => $amount->currency()->getCode(),
            'customer' => $customer->stripe_id,
            'api_key' => $this->apiKey
        ]);

        return app()->make(Charge::class, [$charge]);
    }
}