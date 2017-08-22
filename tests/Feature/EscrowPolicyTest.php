<?php

namespace Makeable\LaravelEscrow\Tests\Feature;

use Makeable\LaravelEscrow\Escrow;
use Makeable\LaravelEscrow\Exceptions\IllegalEscrowAction;
use Makeable\LaravelEscrow\Exceptions\InsufficientFunds;
use Makeable\LaravelEscrow\Tests\Fakes\PassingProductEscrowPolicy;
use Makeable\LaravelEscrow\Tests\Fakes\Product;
use Makeable\LaravelEscrow\Tests\Fakes\Transaction;
use Makeable\LaravelEscrow\Tests\TestCase;
use Mockery;

class EscrowPolicyTest extends TestCase
{
    private function escrow(&$policy)
    {
        Product::$escrowPolicy = $policy;

        return Escrow::init(factory(Product::class)->create());
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
