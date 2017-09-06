<?php

namespace Makeable\LaravelEscrow\Tests\Feature\Interactions;

use Illuminate\Support\Facades\Event;
use Makeable\LaravelEscrow\Events\EscrowCancelled;
use Makeable\LaravelEscrow\Interactions\CancelEscrow;
use Makeable\LaravelEscrow\Tests\DatabaseTestCase;

class CancelEscrowTest extends DatabaseTestCase
{
    /** @test **/
    public function it_fires_escrow_cancelled_event()
    {
        Event::fake();

        $this->interact(CancelEscrow::class, $this->escrow);

        Event::assertDispatched(EscrowCancelled::class, function ($event) {
            return $event->escrow->id === $this->escrow->id;
        });
    }

    /** @test **/
    function it_()
    {

    }
}
