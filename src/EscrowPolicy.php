<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\EscrowPolicyContract;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;

class EscrowPolicy implements EscrowPolicyContract
{
    /**
     * @param $action
     * @param Escrow $escrow
     *
     * @throws IllegalEscrowAction
     */
    public function check($action, $escrow)
    {
        if (!(new static())->$action($escrow)) {
            throw new IllegalEscrowAction($action);
        }
    }

    /**
     * @param Escrow $escrow
     *
     * @return bool
     */
    public function cancel($escrow)
    {
        throw_unless($escrow->checkStatus(new EscrowStatus('committed')), IllegalEscrowAction::class);

        return true;
    }

    /**
     * @param Escrow $escrow
     *
     * @return bool
     */
    public function commit($escrow)
    {
        throw_unless($escrow->checkStatus(new EscrowStatus('open')), IllegalEscrowAction::class);

        return true;
    }

    /**
     * @param Escrow $escrow
     *
     * @return bool
     */
    public function release($escrow)
    {
        throw_unless($escrow->checkStatus(new EscrowStatus('committed')), IllegalEscrowAction::class);

        return true;
    }
}
