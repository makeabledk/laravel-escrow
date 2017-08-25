<?php

namespace Makeable\LaravelEscrow;

use Makeable\LaravelEscrow\Contracts\CustomerContract as Customer;
use Makeable\LaravelEscrow\Contracts\TransactionContract as Transaction;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;
use Makeable\ValueObjects\Amount\Amount;

trait EscrowActions
{
    /**
     * @return bool
     *
     * @throws IllegalEscrowAction
     */
    public function cancel()
    {
        $this->requiresStatus(null);

        if ($this->policy('cancel')) {
            $this->forceFill(['status' => 0])->save();

            return $this->policy('cancelled');
        }

        return false;
    }

    /**
     * @param Customer $customer
     * @return bool
     */
    public function chargeDepositFrom($customer)
    {
        $amount = $this->escrowable->getDepositAmount()->subtract($this->getBalance())->minimum(Amount::zero()); // TODO handle current balnce and no charge if zero
        $charge = $customer->charge($amount->get(), $amount->currency()->code);
        $transaction = app(Transaction::class)
            ->setAmount($amount)
            ->setReference($charge)
            ->setSource($customer);

        return $this->deposit($transaction);
    }

    /**
     * @param Transaction $transaction
     *
     * @throws IllegalEscrowAction
     *
     * @return bool
     */
    public function deposit($transaction)
    {
        $this->requiresStatus(null);

        if ($this->policy('deposit', $transaction) && $this->deposits()->save($transaction)) {
            if ($this->policy('deposited', $transaction)) {
                return $this->isFunded() ? $this->policy('funded') : true;
            }
        }

        return false;
    }

    /**
     * @return bool
     *
     * @throws InsufficientFunds
     * @throws IllegalEscrowAction
     */
    public function release()
    {
        $this->requiresStatus(null);

        if (!$this->isFunded()) {
            throw new InsufficientFunds();
        }

        if ($this->policy('release')) {
            $this->forceFill(['status' => 1])->save();

            return $this->policy('released');
        }

        return false;
    }

    /**
     * @param $action
     * @param array $args
     *
     * @return bool
     */
    protected function policy($action, ...$args)
    {
        return $this->escrowable->escrowPolicy()->$action($this->escrowable, ...$args);
    }

    /**
     * @param $status
     *
     * @return bool
     *
     * @throws IllegalEscrowAction
     */
    protected function requiresStatus($status)
    {
        if ($this->status !== $status) {
            throw new IllegalEscrowAction();
        }

        return true;
    }
}
