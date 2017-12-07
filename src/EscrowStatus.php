<?php

namespace Makeable\LaravelEscrow;

use Illuminate\Database\Query\Builder;
use Makeable\EloquentStatus\Status;

class EscrowStatus extends Status
{
    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function open($query)
    {
        return $query
            ->whereNull('committed_at')
            ->whereNull('released_at')
            ->whereNull('cancelled_at');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function committed($query)
    {
        return $query
            ->whereNotNull('committed_at')
            ->whereNull('released_at')
            ->whereNull('cancelled_at');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function released($query)
    {
        return $query
            ->whereNotNull('committed_at')
            ->whereNotNull('released_at')
            ->whereNull('cancelled_at');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function cancelled($query)
    {
        return $query->whereNotNull('cancelled_at');
    }
}
