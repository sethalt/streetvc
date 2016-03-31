<?php
namespace StreetVC\LoanBundle\Model;

use Finite\Factory\FactoryInterface;
use Finite\StateMachine\StateMachineInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Money\Money;
use Monolog\Logger;
use Sd\BaseBundle\Has\StateManagerTrait;
use StreetVC\LenderBundle\Document\LoanCommitment;
use StreetVC\LoanBundle\Document\Repayment;
use StreetVC\LoanBundle\Document\RepaymentSchedule;
use StreetVC\LoanBundle\Document\RepaymentShare;
use StreetVC\LoanBundle\Repository\EscrowRepository;
use StreetVC\UserBundle\Document\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Persistence\ObjectManager;
use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\LoanBundle\Document\Escrow;
use StreetVC\LoanBundle\Event\FundEscrowEvent;
use StreetVC\BancboxInvest\BancboxInvestBundle\Provider\BancboxProvider;
use StreetVC\LoanBundle\Event\EscrowEvents;

/**
 * @DI\Service("repayment.manager")
 * @author dao
 *
 */
class RepaymentManager
{
    use StateManagerTrait;

    /** @var ObjectManager */
    private $om;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * @var BancboxProvider
     */
    private $bancbox_provider;

    /** @var StateMachineInterface */
    private $sm;

    /**
     * @DI\InjectParams
     * @param ObjectManager $om
     * @param FactoryInterface $finite_factory
     * @param EventDispatcherInterface $dispatcher
     * @param Logger $logger
     * @param BancboxProvider $bancbox_provider
     */
    public function __construct(ObjectManager $om, FactoryInterface $finite_factory, EventDispatcherInterface $dispatcher, Logger $logger, BancboxProvider $bancbox_provider)
    {
        $this->om = $om;
        $this->finite_factory = $finite_factory;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->bancbox_provider = $bancbox_provider;
    }

    public function processRepayments()
    {

    }

    protected function getRepaymentRepository()
    {
        return $this->om->getRepository('StreetVCLoanBundle:RepaymentSchedule');
    }
}
