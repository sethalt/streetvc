<?php

namespace StreetVC\LoanBundle\Event\Listener;

use StreetVC\BorrowerBundle\Event\LoanRequestEvent;
use JMS\DiExtraBundle\Annotation as DI;
use Finite\Event\TransitionEvent;
use Psr\Log\LoggerInterface;
use StreetVC\LoanBundle\Document\Escrow;
use StreetVC\LoanBundle\Document\LoanRequest;
use Finite\StateMachine\StateMachine;
use Doctrine\Common\Persistence\ObjectManager;
use StreetVC\LoanBundle\Event\LoanRequestEvents;
use StreetVC\LoanBundle\Model\LoanRequestManager;
use StreetVC\LoanBundle\Model\PaymentCalculator;
use StreetVC\LoanEvaluation\Bundle\Document\LoanEvaluation;
use StreetVC\BancboxInvest\BancboxInvestBundle\Provider\BancboxProvider;
use StreetVC\LoanEvaluation\LoanEvaluationEvent;
use StreetVC\LoanEvaluation\Service\LoanEvaluatorStub;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/** @DI\Service("streetvc.loanrequest.listener") */
class LoanRequestListener
{
    protected $om;
    protected $logger;
    protected $bancbox;

    /** @DI\InjectParams */
    public function __construct(ObjectManager $om, BancboxProvider $bancbox_provider, LoggerInterface $logger = null) {
        $this->om = $om;
        $this->bancbox_provider = $bancbox_provider;
        $this->logger = $logger;
    }

    /**
     * @DI\Observe("finite.post_transition.submit")
     * after loan request is submitted, automatically transition to evaluate.
     */
    public function postSubmitTransition(TransitionEvent $event)
    {
        $sm = $event->getStateMachine();
        $object = $sm->getObject();
        if ($object instanceof LoanRequest) {
            $object->submit();
            $sm->apply('evaluate');
        }
    }

    /**
     * @DI\Observe("finite.pre_transition.cancel")
     */
    public function postCancel(TransitionEvent $event, $name, EventDispatcherInterface $dispatcher)
    {
        $sm = $event->getStateMachine();
        // @var LoanRequest
        $object = $sm->getObject();
        if ($object instanceof LoanRequest) {
            $business = $object->getBusiness();
            $business->unsetActiveLoanRequest();
        }
    }

    /**
     * @DI\Observe("finite.pre_transition.accept")
     */
    public function preAccept(TransitionEvent $event, $name, EventDispatcherInterface $dispatcher)
    {
        $sm = $event->getStateMachine();
        $object = $sm->getObject();
        if ($object instanceof LoanRequest) {
            $evaluation = $object->getEvaluation();
            $evaluation->accept();
        }
    }

    /**
     * @DI\Observe("finite.pre_transition.evaluate")
     * @todo: move logic to manager
     */
    public function preEvaluate(TransitionEvent $event, $name, EventDispatcherInterface $dispatcher)
    {
        $sm = $event->getStateMachine();
        $object = $sm->getObject();
        if ($object instanceof LoanRequest) {
            /** @var LoanRequest $object */
            /** @var LoanEvaluationEvent $evaluateEvent */
            $evaluateEvent = $dispatcher->dispatch(LoanEvaluationEvent::EVALUATE, new LoanEvaluationEvent($object));
            if ($evaluation = $evaluateEvent->getEvaluation()) {
                $this->om->persist($evaluation);
                $object->setEvaluation($evaluation);
                $termPayment = PaymentCalculator::calculateTermPayment($object->getFundingGoal(), $object->getTerm(), $evaluation->getRate());
                $object->setTermPayment($termPayment);
            } else {
                $event->reject();
            }
        }
    }

    /**
     * @DI\Observe("loanrequest.evaluate")
     * @param LoanEvaluationEvent $event
     * listen for loanrequest.evaluate event and complete evaluation of loan request
     */
    public function doEvaluate(LoanEvaluationEvent $event)
    {
        $evaluation = LoanEvaluatorStub::evaluate($event->getLoanRequest());
        $event->setEvaluation($evaluation);
    }

    /**
     * @DI\Observe("finite.post_transition.evaluate")
     */
    public function postEvaluate(TransitionEvent $event, $name, EventDispatcherInterface $dispatcher)
    {
        $sm = $event->getStateMachine();
        $object = $sm->getObject();
        if ($object instanceof LoanRequest) { }
    }
}
