<?php

namespace Makeable\LaravelEscrow\Tests\Fakes;

use App\User;
use Makeable\LaravelEscrow\Contracts\CustomerContract;
use Makeable\LaravelEscrow\Transactable;

class Customer extends User implements CustomerContract
{
    use Transactable;

    /**
     * @var string
     */
    protected $table = 'users';
}
