<?php

namespace Makeable\LaravelEscrow\Tests\Fakes;

use App\User;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Transactable;

class Provider extends User implements ProviderContract
{
    use Transactable;

    /**
     * @var string
     */
    protected $table = 'users';
}
