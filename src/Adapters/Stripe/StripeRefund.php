<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Makeable\LaravelEscrow\Contracts\RefundContract;

class StripeRefund extends \Makeable\LaravelStripeObjects\StripeRefund implements RefundContract
{
    use HasAmount;
}
