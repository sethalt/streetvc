<?php
//namespace StreetVC\LoanBundle\Tests\Event\Listener;

use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\DependencyInjection\Container;
use Finite\Event\TransitionEvent;
use StreetVC\LoanBundle\Document\Escrow;
use Finite\StateMachine\StateMachine;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use StreetVC\Tests\BaseTestCase;

class EscrowListenerTest extends BaseTestCase
{
    private $factory;

    public function setUp()
    {
//        parent::setUp();
//        self::bootKernel();
//        $container = static::$kernel->getContainer();

//        $this->factory = $this->get('finite.factory');
    }

}