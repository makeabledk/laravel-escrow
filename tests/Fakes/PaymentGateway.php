<?php

namespace Makeable\LaravelEscrow\Tests\Fakes;

use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Contracts\TransferSourceContract;

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
     * @return TransferSourceContract
     */
    public function charge($customer, $amount, $reference = null)
    {
        return $this->handle();
    }

    /**
     * @param ProviderContract $provider
     * @param Amount           $amount
     * @param $reference
     *
     * @return TransferSourceContract
     */
    public function pay($provider, $amount, $reference = null)
    {
        return $this->handle();
    }

    public function shouldFail()
    {
        $this->shouldFail = true;
    }

    /**
     * @return TransferSource
     *
     * @throws \Exception
     */
    public function handle()
    {
        if ($this->shouldFail) {
            throw new \Exception();
        }

        return new TransferSource();
    }
}
