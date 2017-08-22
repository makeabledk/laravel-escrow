<?php

namespace Makeable\LaravelEscrow\Tests\Fakes;

use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Escrowable;
use Makeable\ValueObjects\Amount\Amount;

class Product extends \Illuminate\Database\Eloquent\Model implements EscrowableContract
{
    use Escrowable;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    public static $escrowPolicy = PassingProductEscrowPolicy::class;

    /**
     * @return Amount
     */
    public function getDepositAmount()
    {
        return new Amount(760, 'DKK');
    }
}
