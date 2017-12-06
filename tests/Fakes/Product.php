<?php

namespace Makeable\LaravelEscrow\Tests\Fakes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Makeable\LaravelEscrow\Contracts\EscrowableContract;
use Makeable\LaravelEscrow\Escrowable;
use Makeable\LaravelCurrencies\Amount;

class Product extends \Illuminate\Database\Eloquent\Model implements EscrowableContract
{
    use Escrowable;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return Amount
     */
    public function getDepositAmount()
    {
        return new Amount(250, 'DKK'); // often the deposit will be the platforms' fee
    }

    /**
     * @return Amount
     */
    public function getCustomerAmount()
    {
        return new Amount(1000, 'DKK');
    }

    /**
     * @return Amount
     */
    public function getProviderAmount()
    {
        return new Amount(750, 'DKK');
    }
}
