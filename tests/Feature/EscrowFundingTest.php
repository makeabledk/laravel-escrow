<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Tests\Fakes\Product;
use Makeable\LaravelEscrow\Transaction;

class EscrowFundingTest extends DatabaseTestCase
{
    public function test_it_inits_with_a_product()
    {
        $this->assertTrue($this->escrow instanceof Escrow);
    }

    public function test_balance_consists_of_deposits_and_transactions()
    {
        $withdrawal = factory(Transaction::class)->make();
        $deposit = factory(Transaction::class)->make();

        $this->escrow->withdrawals()->save($withdrawal);
        $this->escrow->deposits()->save($deposit);

        $this->assertTrue($this->escrow->getBalance()->equals($deposit->amount->subtract($withdrawal->amount)));
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
