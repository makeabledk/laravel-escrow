<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelStripeObjects\StripeCustomer;

interface CustomerContract extends TransactableContract
{
    /**
     * @return StripeCustomer
     */
    public function stripeCustomer();
}
