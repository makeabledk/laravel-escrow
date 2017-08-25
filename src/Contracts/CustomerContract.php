<?php

namespace Makeable\LaravelEscrow\Contracts;

interface CustomerContract extends EloquentContract
{
    /**
     * @param  string  $token
     * @param  array  $options
     * @return \Stripe\Customer
     */
    public function createAsStripeCustomer();

    /**
     * @param  int  $amount
     * @param  array  $options
     *
     * @return \Stripe\Charge
     *
     * @throws \InvalidArgumentException
     */
    public function charge($amount, $options);
}