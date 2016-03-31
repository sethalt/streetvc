<?php

namespace StreetVC\LoanBundle\Event\Listener;

use JMS\DiExtraBundle\Annotation as DI;
use Finite\Event\TransitionEvent;
use Psr\Log\LoggerInterface;
use StreetVC\BancboxInvest\Event\WebhookEvent;
use StreetVC\LoanBundle\Document\Escrow;
use StreetVC\LoanBundle\Event\FundEscrowEvent;
use StreetVC\LoanBundle\Model\EscrowManager;
use StreetVC\LoanBundle\Model\EscrowTransitions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/** @DI\Service("streetvc.escrow.listener") */
class EscrowListener
{
    protected $manager;
    protected $logger;

    /**
     * @DI\InjectParams
     * @param EscrowManager $escrow_manager
     * @param LoggerInterface $logger
     */
    public function __construct(EscrowManager $escrow_manager, LoggerInterface $logger = null) {
        $this->manager = $escrow_manager;
        $this->logger = $logger;
    }

    /**
     * @DI\Observe("finite.post_transition.cancel")
     * @param TransitionEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public function postCancelTransition(TransitionEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $sm = $event->getStateMachine();
        $object = $sm->getObject();
        if ($object instanceof Escrow) {
            $object->getBusiness()->unsetActiveEscrow($object);
            // @todo this be in manager
        }
    }

    /**
     * @DI\Observe("finite.pre_transition.funded")
     * @param TransitionEvent $event
     */
    public function preFundedTransition(TransitionEvent $event)
    {
        /** @var Escrow $escrow */
        $escrow = $event->getStateMachine()->getObject();
        $this->manager->generateRepaymentSchedule($escrow);

    }

    public function preCloseTransition(TransitionEvent $event)
    {
    }

    /**
     * @DI\Observe("finite.post_transition.disburse")
     * @param TransitionEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public function postDisburse(TransitionEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
    }

    /**
     * @DI\Observe("finite.post_transition.close_confirmed")
     * @param TransitionEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public function postCloseConfirmed(TransitionEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $sm = $event->getStateMachine();
        $sm->apply(EscrowTransitions::SCHEDULE_PAYMENTS);
    }

    /**
     * @DI\Observe("finite.pre_transition")
     * @DI\Observe("finite.post_transition")
     * @param TransitionEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public function logEvent(TransitionEvent $event, $eventName, EventDispatcherInterface $dispatcher = null)
    {
        $sm = $event->getStateMachine();
        $object = $sm->getObject();
        $transition = $event->getTransition();
        $class = (new \ReflectionClass($object))->getShortName();

        $name = $transition->getName();
        $id = $object->getId();
        $msg = "$eventName.$name $class::$id";
        $this->logger->notice($msg);
    }

    /**
     * @DI\Observe("finite.pre_transition.schedule_payments")
     * @param TransitionEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public function preSchedulePaymentsTransition(TransitionEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        /** @var Escrow $escrow */
        $escrow = $event->getStateMachine()->getObject();
        $this->manager->createPaymentSchedules($escrow);
    }

    /**
     * @DI\Observe("finite.post_transition.schedule_payments")
     * @param TransitionEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public function postSchedulePaymentsTransition(TransitionEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $event->getStateMachine()->apply(EscrowTransitions::REPAYING);
    }

    /**
     * @DI\Observe("escrow.funded")
     * @param FundEscrowEvent $event
     * @todo testme
     */
    public function onEscrowFunded(FundEscrowEvent $event)
    {
        $escrow = $event->getEscrow();
        $this->manager->checkFunding($escrow);
    }

    /**
     * @DI\Observe("bancbox.webhook.OPEN_ESCROW")
     * transition to 'open' state
     * @param WebhookEvent $event
     */
    public function onOpenEscrowWebhook(WebhookEvent $event)
    {
        $escrow = $this->manager->findByBancboxId($event->getParam('escrow_id'));
        $this->manager->confirmOpen($escrow);
    }

    /**
     * @DI\Observe("bancbox.webhook.CLOSE_ESCROW")
     * @param WebhookEvent $event
     */
    public function onCloseEscrowWebhook(WebhookEvent $event)
    {
        $escrow = $this->manager->findByBancboxId($event->getParam('escrow_id'));
        $this->manager->confirmClose($escrow);
    }

    /**
     * @DI\Observe("bancbox.webhook.CANCELLED_ESCROW")
     * @todo test
     * @param WebhookEvent $event
     */
    public function onCancelledEscrow(WebhookEvent $event)
    {
        $escrow = $this->manager->findByBancboxId($event->getParam('escrow_id'));
        $this->manager->confirmCancel($escrow);
    }

    /**
     * @param WebhookEvent $event
     * @DI\Observe("bancbox.webhook.FUND_ESCROW")
     */
    public function onEscrowFundedWebhook(WebhookEvent $event)
    {
//        $escrow = $this->manager->findByBancboxId($event->getParam('escrow_id'));
        $this->logger->notice("No action implemented for FUND_ESCROW webhook");
    }
}
