<?php

namespace Makeable\LaravelEscrow\Tests\Fakes;

use App\User;
use Makeable\LaravelEscrow\Contracts\ProviderContract;
use Makeable\LaravelEscrow\Transactable;
use Makeable\LaravelStripeObjects\HasStripeAccount;

class Provider extends User implements ProviderContract
{
    use HasStripeAccount, Transactable;

    /**
     * @var string
     */
    protected $table = 'users';
}
