<?php

namespace Makeable\LaravelEscrow\Tests\Fakes;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Contracts\TransferSourceContract;
use Makeable\LaravelStripeObjects\StripeCharge;
use Makeable\LaravelStripeObjects\StripeObject;
use Makeable\LaravelStripeObjects\StripeTransfer;
use Stripe\Charge;
use Stripe\Transfer;

class PaymentGateway implements PaymentGatewayContract
{
    /**
     * @var bool
     */
    protected $shouldFail = false;

    /**
     * @param CustomerContract $customer
     * @param Amount           $amount
     * @param $reference
     *
     * @return StripeObject
     */
    public function charge($customer, $amount, $reference = null)
    {
        $this->maybeFail();

        return StripeCharge::createFromObject(new Charge(uniqid()));
    }

    /**
     * @param ProviderContract $provider
     * @param Amount           $amount
     * @param $reference
     *
     * @return StripeObject
     */
    public function pay($provider, $amount, $reference = null)
    {
        $this->maybeFail();

        return StripeTransfer::createFromObject(new Transfer(uniqid()));
    }

    /**
     * @return PaymentGateway
     */
    public function shouldFail()
    {
        $this->shouldFail = true;

        return $this;
    }

    /**
     * @return PaymentGateway
     *
     * @throws \Exception
     */
    public function maybeFail()
    {
        if ($this->shouldFail) {
            throw new \Exception();
        }

        return $this;
    }
}
