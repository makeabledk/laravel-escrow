<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use BadMethodCallException;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Contracts\RefundableContract;
use Makeable\LaravelEscrow\Contracts\RefundContract;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Events\RefundCreated;
use Stripe\Stripe;

class StripePaymentGateway implements PaymentGatewayContract
{
    /**
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        Stripe::setApiKey($apiKey);
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
        $amount = $amount->minimum((new Amount(5, 'DKK'))->convertTo($amount->currency()));

        $options = [
            'amount' => $amount->toCents(),
            'currency' => $amount->currency()->getCode(),
            'customer' => $customer->stripeCustomer()->id,
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
            'amount' => $amount->toCents(),
            'currency' => $amount->currency()->getCode(),
            'destination' => $provider->stripeAccount()->id,
        ];

        if ($associatedEscrow) {
            $options['transfer_group'] = $associatedEscrow->identifier;
        }

        return StripeTransfer::createFromObject(\Stripe\Transfer::create($options));
    }

    /**
     * @param RefundableContract $refundable
     * @param Amount|null        $amount
     *
     * @return \Makeable\LaravelStripeObjects\StripeObject|RefundContract
     */
    public function refund($refundable, $amount = null)
    {
        $options = [
            'amount' => $amount ? $amount->convertTo($refundable->currency)->toCents() : null,
        ];

        if ($refundable instanceof StripeCharge) {
            $refund = StripeRefund::createFromObject($refundable->retrieve()->refunds->create($options));
        } elseif ($refundable instanceof StripeTransfer) {
            $refund = StripeTransferReversal::createFromObject($refundable->retrieve()->reversals->create($options));
        } else {
            throw new BadMethodCallException("Stripe payment gateway can't refund ".get_class($refundable));
        }

        RefundCreated::dispatch($refund, $refundable);

        return $refund;
    }
}
