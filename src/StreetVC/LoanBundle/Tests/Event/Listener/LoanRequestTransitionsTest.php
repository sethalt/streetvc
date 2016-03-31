<?php
//$dir = __DIR__.'/../../../../vendor/yohang/finite/tests';
//$loader = require $dir.'/autoload.php';
//$loader->add('Finite\Test', $dir);

//use \Finite\Test\StateMachine\ListenableStateMachineTest;

class LoanRequestTransitionsTest // extends ListenableStateMachineTest
{
    public function setUp()
    {
//        parent::setup();
    }

    public function testEscrowIsCreatedOnAccepted()
    {
        echo "hi";

/*
    $this->dispatcher
    ->expects($this->at(1))
    ->method('dispatch')
    ->with('finite.test_transition', $this->isInstanceOf('Finite\Event\TransitionEvent'));

    $this->dispatcher
    ->expects($this->at(2))
    ->method('dispatch')
    ->with('finite.test_transition.t23', $this->callback(function($event) {
        $event->reject();
        return $event instanceof \Finite\Event\TransitionEvent;
    }));

        $this->initialize();
        $this->assertFalse($this->object->can('t34'));
        $this->assertFalse($this->object->can('t23'));
    */
    }
}