<?php

namespace Makeable\LaravelEscrow\Tests\Fakes;

use App\User;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Transactable;
use Makeable\LaravelStripeObjects\HasStripeCustomer;

class Customer extends User implements CustomerContract
{
    use HasStripeCustomer, Transactable;

    /**
     * @var string
     */
    protected $table = 'users';
}
