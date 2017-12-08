<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Database\Eloquent\Model;
use Makeable\LaravelEscrow\Contracts\SalesAccountContract;

/**
 * Class SalesAccount.
 *
 * A read-only class that holds funds and lets you query transactions
 */
class SalesAccount extends Model implements SalesAccountContract
{
    use Transactable;

    /**
     * @var array
     */
    public $attributes = [
        'id' => 1,
    ];

    /**
     * @return string
     */
    public function getMorphClass()
    {
        return get_class($this);
    }
}
