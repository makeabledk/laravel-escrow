<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;
use Makeable\LaravelEscrow\Tests\Fakes\Product;
use Makeable\LaravelEscrow\Transaction;
use Makeable\LaravelEscrow\Tests\TestCase;

class EscrowTest extends TestCase
{
    private function escrow()
    {
        return Escrow::init(factory(Product::class)->create());
    }

    public function test_it_inits_with_a_product()
    {
        $this->assertTrue($this->escrow() instanceof Escrow);
    }

    public function test_it_can_have_amounts_deposited_through_transactions()
    {
        $transaction = factory(Transaction::class)->make();
        $escrow = $this->escrow();
        $escrow->deposit($transaction);

        $this->assertTrue($transaction->amount->equals($escrow->getBalance()));
    }

    public function test_the_balance_also_includes_withdrawals()
    {
        $withdrawal = factory(Transaction::class)->make();
        $deposit = factory(Transaction::class)->make();

        $escrow = $this->escrow();
        $escrow->withdrawals()->save($withdrawal);
        $escrow->deposits()->save($deposit);

        $this->assertTrue($escrow->getBalance()->equals($deposit->amount->subtract($withdrawal->amount)));
    }

    public function test_it_cannot_be_release_until_funded()
    {
        $escrow = $this->escrow(); // deposit fund 760 DKK

        // No funds
        $this->expectException(InsufficientFunds::class);
        $escrow->release();

        // 750 DKK
        $escrow->deposit(factory(Transaction::class)->make(['amount' => 100, 'currency' => 'EUR']));
        $this->expectException(InsufficientFunds::class);
        $escrow->release();

        // 760 DKK
        $escrow->deposit(factory(Transaction::class)->make(['amount' => 10, 'currency' => 'DKK']));
        $escrow->release();

        $this->assertEquals(1, $escrow->status);
    }

    public function test_it_can_hold_more_funds_than_required()
    {
        $escrow = $this->escrow(); // deposit fund 760 DKK
        $escrow->deposit(factory(Transaction::class)->make(['amount' => 1000, 'currency' => 'DKK']));
        $escrow->release();
    }

    public function test_it_can_only_release_or_cancel_when_open()
    {
        $escrow = $this->escrow();
        $escrow->status = 1;
        $escrow->save();

        $this->expectException(IllegalEscrowAction::class);
        $escrow->cancel();

        $this->expectException(IllegalEscrowAction::class);
        $escrow->release();
    }
}
