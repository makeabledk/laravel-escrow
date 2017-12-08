<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelStripeObjects\StripeAccount;

interface ProviderContract extends TransactableContract
{
    /**
     * @return StripeAccount
     */
    public function stripeAccount();
}
