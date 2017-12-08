<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Makeable\LaravelEscrow\Contracts\RefundContract;

class StripeTransferReversal extends \Makeable\LaravelStripeObjects\StripeTransferReversal implements RefundContract
{
    use HasAmount;
}
