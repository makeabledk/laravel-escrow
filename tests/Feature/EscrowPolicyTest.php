<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;
use Makeable\LaravelEscrow\Tests\Fakes\PassingProductEscrowPolicy;
use Makeable\LaravelEscrow\Tests\Fakes\Product;
use Makeable\LaravelEscrow\Transaction;
use Makeable\LaravelEscrow\Tests\TestCaseOrchestra;
use Mockery;

class EscrowPolicyTest extends TestCaseOrchestra
{
    private function escrow(&$policy)
    {
        Product::$escrowPolicy = $policy;

        return Escrow::init(factory(Product::class)->create());
    }

    public function test_empty()
    {
        $this->assertTrue(true);
    }

//
//    public function test_policy_received_cancellation_callbacks()
//    {
//        $policy = Mockery::spy(PassingProductEscrowPolicy::class);
//        $escrow = $this->escrow($policy);
//
////        $transaction = factory(Transaction::class)->make(['amount' => 1000]);
//
//        $escrow->cancel();
////        $escrow->deposit($transaction);
//
//        $policy->shouldHaveReceived('cancel')->with($escrow)->once();
//    }

}
