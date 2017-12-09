<?php

namespace Makeable\LaravelEscrow\Tests;

use Makeable\LaravelEscrow\Contracts\PaymentGatewayContract;
use Makeable\LaravelEscrow\Tests\Fakes\PaymentGateway;

trait FakePaymentGateway
{
    public function setUp()
    {
        parent::setUp();

        // Bind fake payment gateway
        app()->singleton(PaymentGatewayContract::class, PaymentGateway::class);
    }
}
