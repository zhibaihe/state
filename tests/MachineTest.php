<?php

use Zhibaihe\State\Machine;

class MachineTest extends PHPUnit_Framework_TestCase {

    protected $machine;

    /** @test */
    public function it_can_be_initialized()
    {
        $this->assertInstanceOf(Machine::class, new Machine);
    }

    /** @test */
    public function it_loads_machine_configuration_from_string()
    {
        $machine = $this->makeMachine();

        $states = $machine->states();
        $transitions = $machine->transitions();

        $this->assertCount(4, $states);
        $this->assertCount(3, $transitions);

        $this->assertEquals(['draft', 'pending', 'published', 'archived'], $states);
        $this->assertEquals([
            'draft' => ['pend' => 'pending'],
            'pending' => ['publish' => 'published'],
            'published' => ['archive' => 'archived'],
        ], $transitions);

        $this->assertEquals('draft', $machine->state());
    }

    /** @test */
    public function it_processes_valid_transitions()
    {
        $machine = $this->makeMachine();

        $machine->pend();
        $this->assertEquals('pending', $machine->state());

        $machine->publish();
        $this->assertEquals('published', $machine->state());

        $machine->archive();
        $this->assertEquals('archived', $machine->state());
    }

    protected function makeMachine()
    {
        return new Machine("states: draft, pending, published, archived
                            - pend: draft > pending
                            - publish: pending > published
                            - archive: published > archived");
    }
}