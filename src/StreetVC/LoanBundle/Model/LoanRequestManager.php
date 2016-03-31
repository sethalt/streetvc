<?php
namespace StreetVC\LoanBundle\Model;

use JMS\DiExtraBundle\Annotation as DI;
use Sd\BaseBundle\Has\StateManagerTrait;
use StreetVC\BaseBundle\Document\Contract;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Persistence\ObjectManager;
use StreetVC\LoanBundle\Document\LoanRequest;
use Finite\Factory\FactoryInterface;
use Finite\StateMachine\StateMachineInterface;

/**
 * @DI\Service("loanrequest.manager")
 * @author dao
 *
 */
class LoanRequestManager
{
    use StateManagerTrait;

    /** @var ObjectManager */
    private $om;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var FactoryInterface */
    private $finite_factory;

    /** @var StateMachineInterface */
    private $sm;

    /** @var string */
    private $graph;

    private $class = 'StreetVC\LoanBundle\Document\LoanRequest';

    /**
     * @DI\InjectParams()
     *
     * @param ObjectManager $om
     * @param EventDispatcherInterface $dispatcher
     * @param FactoryInterface $finite_factory;
     */
    public function __construct(ObjectManager $om, EventDispatcherInterface $dispatcher, FactoryInterface $finite_factory)
    {
        $this->om = $om;
        $this->dispatcher = $dispatcher;
        $this->finite_factory = $finite_factory;
        $this->graph = 'default';
    }

    public function create()
    {
        return new $this->class();
    }

    public function getClass()
    {
        return $this->class;
    }

    public function validateLoanRequest(LoanRequest $loanRequest)
    {
        if (!$loanRequest->isValid()) {
            $violations = $loanRequest->validate();
            throw new \Exception("Attempt to create escrow from invalid Loan Request: ".(string) $violations);
        }
        return true;
    }

    public function evaluateLoanRequest(LoanRequest $loanRequest)
    {

    }

    public function submit(LoanRequest $loanRequest)
    {
        $this->applyTransition($loanRequest, 'submit');
        $this->flush();
    }

    public function cancel(LoanRequest $loanRequest)
    {
        $this->applyTransition($loanRequest, 'cancel');
        $this->flush();
    }

    public function acceptTerms(LoanRequest $loanRequest)
    {
        $this->applyTransition($loanRequest, 'accept');
        $this->flush();
    }

    public function flush() {
        $this->om->flush();
    }

}
