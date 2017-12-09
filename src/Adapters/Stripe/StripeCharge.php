<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Makeable\LaravelEscrow\Contracts\RefundableContract;

class StripeCharge extends \Makeable\LaravelStripeObjects\StripeCharge implements RefundableContract
{
}
