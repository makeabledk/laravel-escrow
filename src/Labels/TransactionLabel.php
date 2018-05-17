<?php

namespace Makeable\LaravelEscrow\Labels;

use Illuminate\Database\Eloquent\Relations\Relation;
use Makeable\LaravelEscrow\Transaction;

trait TransactionLabel
{
    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @param Transaction $transaction
     */
    public function bindTransaction(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        $morphMap = Relation::morphMap();

        if (! empty($morphMap) && in_array(static::class, $morphMap)) {
            return array_search(static::class, $morphMap, true);
        }

        return static::class;
    }
}
