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
use StreetVC\LoanBundle\Event\EscrowEvent;
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
 * @DI\Service("escrow.manager")
 * @author dao
 *
 */
class EscrowManager
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

    /** @var string */
    private $graph;

    /** @var Logger  */
    private $logger;

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
        $this->graph = 'escrow';
    }

    public function createEscrow(LoanRequest $loanRequest)
    {
        $sm = $this->finite_factory->get($loanRequest, 'default');
        $sm->apply('create_escrow');
//        $this->applyTransition($loanRequest, 'create_escrow');
        /** @todo these shouldn't be here. move to 'accept' transition */
        $loanRequest->setStartDate(new \DateTime());
        $loanRequest->setCloseDate(new \DateTime("+30 days"));

        try {
            if ($violations = (string) $loanRequest->validate()) {
                $msg = 'Error validating loan request: '. $violations;
                throw new \Exception($msg);
            }

            // @todo make work without bancbox
//            $response = $this->bancbox_provider->openEscrow($loanRequest);
//            $loanRequest->setBancboxId($response['id']);
            $escrow = $loanRequest->createEscrow();
            $this->om->persist($escrow);
            $this->flush();
            $this->confirmOpen($escrow);
            /** @todo allocate issuer and platform shares on evaluation. or when amount is set. */
            $this->allocateIssuerAndPlatformShares($escrow);
            $this->flush();
            return $escrow;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Escrow $escrow
     */
    public function confirmOpen(Escrow $escrow)
    {
        $this->applyTransition($escrow, EscrowTransitions::OPEN_CONFIRMED);
        $this->flush();
    }

    /**
     * @param Escrow $escrow
     * @throws \Exception
     */
    public function close(Escrow $escrow, $reason = '')
    {
        $this->collectOriginationFee($escrow);
//        $this->applyTransition($escrow, EscrowTransitions::CLOSE);
        try {
            $this->bancbox_provider->closeEscrow($escrow->getBancboxId(), $reason = 'close requested');
        } catch (\Exception $e) {
            throw $e;
        }

        $this->flush();
    }

    /**
     * @param Escrow $escrow
     * @param string $reason
     * @return mixed
     * @throws \Exception
     */
    public function cancel(Escrow $escrow, $reason = '')
    {
        try {
            $this->applyTransition($escrow, EscrowTransitions::CANCEL);
            $escrow_id = $escrow->getBancboxId();
            $reason = $reason ?: 'User action.';
            $response = $this->bancbox_provider->cancelEscrow($escrow_id, $reason);
        } catch (\Exception $e) {
            throw $e;
        }
        return $response;
    }

    public function finalize(Escrow $escrow)
    {
        $this->allocateIssuerAndPlatformShares($escrow);
//        $this->collectOriginationFee($escrow);
        $this->disburse($escrow);
    }

    public function collectOriginationFee(Escrow $escrow)
    {
        $this->applyTransition($escrow, EscrowTransitions::PAY_ORIGINATION_FEE);
        $this->allocateIssuerAndPlatformShares($escrow);
        if ($escrow->getPlatformFeeCollected()) {
            throw new \Exception('Platform fee already collected');
        }
        $fee = $escrow->getPlatformFee();
        try {
            $response = $this->debitPlatformFee($escrow, $fee);
            $escrow->setPlatformFeeCollected();
            $this->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Escrow $escrow
     * @throws \Exception
     */
    public function disburse(Escrow $escrow)
    {
        $this->applyTransition($escrow, EscrowTransitions::DISBURSE);

        try {
            $response = $this->bancbox_provider->disburseEscrow($escrow);
            $escrow->disburse();
            $escrow->setDisbursement($response->toArray());
            $this->flush();
        } catch (\Exception $e) {
            throw $e;
        }
        $event = new EscrowEvent($escrow);
        $this->dispatcher->dispatch(EscrowEvents::DISBURSED, $event);
        // @todo flush if necessary
    }

    /**
     * @param Escrow $escrow
     * @return bool
     */
    public function confirmCancel(Escrow $escrow)
    {
        try {
            $this->applyTransition($escrow, EscrowTransitions::CANCEL_CONFIRMED);
        } catch (\Exception $e) {
            return false;
        }
        $this->flush();
        return true;
    }

    public function confirmClose(Escrow $escrow)
    {
        try {
            $this->applyTransition($escrow, EscrowTransitions::CLOSE_CONFIRMED);
        } catch (\Exception $e) {
            return false;
        }
        $this->flush();
        return true;
    }

    /**
     * @param Escrow $escrow
     * @return array
     * @throws \Exception
     * request creation of payment schedules with bancbox after escrow is confirmed closed via webhook
     * next: transition to loan?
     */
    public function createPaymentSchedules(Escrow $escrow)
    {
        if (!$schedule = $escrow->getRepaymentSchedule()) {
            $schedule = $this->generateRepaymentSchedule($escrow);
        }
        $schedule = $escrow->getRepaymentSchedule();
        $this->prepareScheduleBatches($schedule);
        $repayments = $schedule->getRepayments();
        /** @var Repayment $repayment */
        foreach ($repayments as $r => $repayment) {
            $request = $repayment->getRequest();
            $response = $this->bancbox_provider->createProceedsSchedules($request)->toArray();
            $items = $response['proceeds_schedule_items'];
            foreach ($items as $item) {
                /** @var RepaymentShare $share */
                if ($share = $repayment->getShareByInvestorId($item['investor_id'])) {
                    $share->setScheduleId($item['schedule_id']);
                };
            }
            $repayment->setRequestId($response['request_id']);
            $repayment->setResponse($response);
            $repayment->setProceedsBatchId($response['proceeds_batch_id']);
        }
        $this->flush();
    }

    /**
     * @param Escrow $escrow
     * @return RepaymentSchedule
     */
    public function generateRepaymentSchedule(Escrow $escrow)
    {
        if ($schedule = $escrow->getRepaymentSchedule()) {
            return $schedule;
        }
        $start = new \DateTime(); // $escrow->getDisbursedOn();
        $interval = new \DateInterval('P1D');

        $term = (int) $escrow->getTerm();
        $period = new \DatePeriod($start, $interval, $term, \DatePeriod::EXCLUDE_START_DATE);
        $principal = $escrow->getFundingGoal();

        $rate = $escrow->getInterestRate();
        if (!$term_payment = $escrow->getTermPayment()) {
            $term_payment = PaymentCalculator::calculateTermPayment($principal, $term, $rate);
            $escrow->setTermPayment($term_payment);
        }

        $schedule = new RepaymentSchedule($escrow);

        $shares = [];
        foreach ($escrow->getCommitments() as $commitment) {
            $fraction = $commitment->getAmount() / $principal;
            $amount = round($term_payment * $fraction, 2);

            $lender = $commitment->getLender();
            $share = new RepaymentShare($lender, $amount);
            $shares[] = $share;
        }
        $schedule->setShares($shares);

        foreach ($period as $d => $date) {
            $repayment = new Repayment();
            $repayment->setShares($shares);
            $repayment->setDate($date);
            $repayment->setAmount(round($term_payment, 2));
            $schedule->addRepayment($repayment);
        }
        $escrow->setRepaymentSchedule($schedule);
        $this->om->persist($schedule);
        $this->flush();
        return $schedule;
    }

    public function prepareScheduleBatches(RepaymentSchedule $schedule) {
        $batches = [];
        /** @var Repayment $repayment  */
        foreach ($schedule->getRepayments() as $r => $repayment) {
            $date = $repayment->getDate();
            $schedule_date = $date->format('Y-m-d');
            $batch = [];
            $batch['escrow_id'] = $schedule->getEscrowId();
            $batch['issuer_id'] = $schedule->getIssuerId();
            $batch['investor_id'] = [];
            $batch['amount'] = [];
            $batch['schedule_date'] = [];
            $shares = $repayment->getShares();
            /** @var RepaymentShare $share */
            foreach ($shares as $s=>$share) {
                $batch['investor_id'][$s] = $share->getInvestorId(); // $share['investor_id'];
                $batch['amount'][$s] = $share->getAmount(); //$share['amount'];
                $batch['schedule_date'][$s] = $schedule_date;
                $batch['memo'][$s] = "schedule $r";
            }
            $batch['schedule_count'] = count($shares);
            $batch['represented_signature'] = 'hey';
            $batch['client_ip_address'] = '127.0.0.1';
            $batch['text'] = 'test text';
            $batch['submit_timestamp'] = date('Y-m-d H:i:s');
            $repayment->setRequest($batch);
            $batches[] = $batch;
        }
        return $batches;
    }
    /**
     * @param Escrow $escrow
     * @param $amount
     * @return mixed
     * @throws \Exception
     * charge a fee
     */
    public function debitPlatformFee(Escrow $escrow, $amount)
    {
        $debitFee = [
            'entity_id' => $escrow->getIssuerId(),
            'entity_type' => 'ISSUER',
            'amount' => $amount,
            'on_availability' => true
        ];

        try {
            $response = $this->bancbox_provider->debitFee($debitFee)->toArray();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw $e;
        }
        // @todo dispatch fee transaction event
        // $response['status' => 'COMPLETED', 'request_id' => 1111, 'event_id' => "1111"]
        return $response;
    }
    /**
     * @param Escrow $escrow
     * @param User $user
     * @param $amount
     * @return LoanCommitment
     * @throws \Exception
     * @todo verify that escrow can receive funds, investor has sufficient balance, etc
     */
    public function fundEscrow(Escrow $escrow, User $user, $amount)
    {
        $amount = intval($amount);
        if (!$amount) {
            throw new \Exception("Invalid investment amount.");
        }
        if ($user->getLender()->getCurrentBalance() < $amount) {
            throw new \Exception("Insufficient funds.");
        }
        if ($amount > $escrow->getAmountRemaining()) {
            throw new \Exception("Investment would exceed the escrow funding limit (remaining: $amount; amount: $amount.");
        }
        if ($amount < $escrow->getMinimumFundingAmount()) {
            throw new \Exception("Investment must be at least ".$escrow->getMinimumFundingAmount());
        }
        if ($amount > $escrow->getMaximumFundingAmount()) {
            throw new \Exception("Investment must be less than ".$escrow->getMaximumFundingAmount());
        }
        $willFund = ($amount == $escrow->getAmountRemaining());
        $commitment = new LoanCommitment($escrow);
        $lender = $user->getLender();
        $commitment->setLender($lender);
        $commitment->setAmount($amount);
        try {
            $lender->withdrawFunds($commitment->getAmount());
            $escrow->addCommitment($commitment);
//            $response = $this->bancbox_provider->fundEscrow($commitment);
//            $commitment->setResponse($response->toArray());
            /**
             * @todo balance should be result of adding transaction to ledger
             */
            $commitment->setFiniteState('confirmed');
            $this->om->persist($commitment);
            $this->flush();
            if ($willFund) {
                $this->logger->notice('will fund '.$escrow->getId());
            }
            $this->dispatcher->dispatch(EscrowEvents::FUNDED, new FundEscrowEvent($commitment));
        } catch (\Exception $e) {
            throw $e;
        }
        return $commitment;
    }

    public function checkFunding(Escrow $escrow)
    {
        $t = EscrowTransitions::FUNDED;
        if ($this->canTransition($escrow, $t) && $escrow->isFullyFunded()) {
            $this->applyTransition($escrow, EscrowTransitions::FUNDED);
            $this->flush();
        }
    }

    /**
     * @return EscrowRepository
     */
    public function getRepository()
    {
        return $this->om->getRepository('StreetVCLoanBundle:Escrow');
    }

    /**
     * @todo reactivate
     */
    private function flush()
    {
        $this->om->flush();
    }

    /**
     * @param Escrow $escrow
     * should not persist
     * @todo move to own service. calculate when we know funding goal of loan request
     */
    private function allocateIssuerAndPlatformShares(Escrow $escrow)
    {
        $goal = $escrow->getFundingGoal();

        $money = Money::USD((int) ($goal*100));
        list ($platformShare, $issuerShare) = $money->allocate([5, 95]);

        $escrow->setPlatformFee($platformShare->getAmount()/100);
        $escrow->setIssuerShare($issuerShare->getAmount()/100);
    }

    /**
     * @param $id
     * @throws \Exception
     * @return Escrow
     */
    public function findByBancboxId($id)
    {
        /* @var Escrow $escrow */
        if (!$escrow = $this->getRepository()->findOneByBancboxId($id)) {
            throw new \Exception("Could not find escrow by bancbox id $id");
        }
        return $escrow;
    }

    public function cancelExpiringEscrows()
    {
        $expiring = $this->getRepository()->getExpiringEscrows();
        foreach ($expiring as $escrow) {
            $this->cancel($escrow, 'expired');
        }
    }
}
