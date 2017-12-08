<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use BadMethodCallException;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Contracts\RefundableContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\RefundCreated;

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
     * @param Escrow | null    $associatedEscrow
     *
     * @return StripeCharge
     */
    public function charge($customer, $amount, $associatedEscrow = null)
    {
        $options = [
            'amount' => $amount->get(),
            'currency' => $amount->currency()->getCode(),
            'customer' => $customer->stripeCustomer()->id,
            'api_key' => $this->apiKey,
        ];

        if ($associatedEscrow) {
            $options['transfer_group'] = $associatedEscrow->identifier;
        }

        return StripeCharge::createFromObject(\Stripe\Charge::create($options));
    }

    /**
     * @param ProviderContract $provider
     * @param Amount           $amount
     * @param Escrow | null    $associatedEscrow
     *
     * @return StripeTransfer
     */
    public function pay($provider, $amount, $associatedEscrow = null)
    {
        $options = [
            'amount' => $amount->get(),
            'currency' => $amount->currency()->getCode(),
            'destination' => $provider->stripeAccount()->id,
            'api_key' => $this->apiKey,
        ];

        if ($associatedEscrow) {
            $options['transfer_group'] = $associatedEscrow->identifier;
        }

        return StripeTransfer::createFromObject(\Stripe\Transfer::create($options));
    }

    /**
     * @param RefundableContract $refundable
     * @param Amount|null $amount
     * @return \Makeable\LaravelStripeObjects\StripeObject
     */
    public function refund($refundable, $amount = null)
    {
        $class = get_class($refundable);

        $options = [
            'amount' => $amount ? $amount->convertTo($refundable->currency)->get() * 100 : null,
            'api_key' => $this->apiKey
        ];

        if ($class instanceof StripeCharge) {
            $refund = $refundable->retrieve()->refund($options);
        }
        elseif ($class instanceof StripeTransfer) {
            $refund = $refundable->retrieve()->reverse($options);
        }
        else {
            throw new BadMethodCallException("Stripe payment gateway can't refund {$class}");
        }

        return tap($class::createFromObject($refund), function ($refund) use ($refundable) {
            RefundCreated::dispatch($refund, $refundable);
        });
    }
}
