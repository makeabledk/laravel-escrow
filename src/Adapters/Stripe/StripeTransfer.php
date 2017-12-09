<?php

namespace Makeable\LaravelEscrow\Adapters\Stripe;

use Makeable\LaravelEscrow\Contracts\RefundableContract;

class StripeTransfer extends \Makeable\LaravelStripeObjects\StripeTransfer implements RefundableContract
{
}
