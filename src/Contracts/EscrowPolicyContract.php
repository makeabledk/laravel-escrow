<?php

namespace Makeable\LaravelEscrow\Contracts;

use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;

interface EscrowPolicyContract
{
    /**
     * @param $action
     * @param $escrow
     *
     * @throws IllegalEscrowAction
     */
    public function check($action, $escrow);

    /**
     * @param Escrow $escrow
     *
     * @return bool
     */
    public function cancel($escrow);

    /**
     * @param Escrow $escrow
     *
     * @return bool
     */
    public function commit($escrow);

    /**
     * @param Escrow $escrow
     *
     * @return bool
     */
    public function release($escrow);
}
