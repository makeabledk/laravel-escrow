<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Makeable\LaravelCurrencies\Amount;
use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;
use Makeable\LaravelEscrow\Tests\Fakes\Product;
use Makeable\LaravelEscrow\Transaction;

class ReleaseEscrowTest extends DatabaseTestCase
{
    /** @test **/
    function it_cant_release_until_committed()
    {
        $this->expectException(IllegalEscrowAction::class);
        $this->escrow->release();

        $this->escrow->commit()->release();
        $this->assertEquals('committed', $this->escrow->status->get());
    }

    /** @test **/
    function it_cant_release_if_funds_unavailable()
    {
        $this->escrow->commit();

        // make fail

        $this->expectException(\Exception::class);
        $this->escrow->release();
    }

    /** @test **/
    function it_transfers_funds_to()
    {

    }

    public function test_it_cannot_be_released_until_funded()
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
