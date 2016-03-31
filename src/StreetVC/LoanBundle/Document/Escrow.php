<?php
namespace StreetVC\LoanBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\Common\Collections\ArrayCollection;
use Sd\BaseBundle\Has\CreatedAndUpdatedTrait;
use Sd\BaseBundle\Has\IdentifiableTrait;
use StreetVC\BaseBundle\Document\Contract;
use StreetVC\LenderBundle\Document\LoanCommitment;
use StreetVC\LenderBundle\Model\LoanCommitmentInterface;
use StreetVC\LoanBundle\Model\EscrowInterface;
use StreetVC\LoanBundle\Model\Traits\TermLoanTrait;
use StreetVC\LoanEvaluation\Model\LoanRequestInterface;
use StreetVC\TransactionBundle\Traits\FiniteStateTrait;
use StreetVC\UserBundle\Document\User;
use StreetVC\BorrowerBundle\Document\Business;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(repositoryClass="StreetVC\LoanBundle\Repository\EscrowRepository")
 * @JMS\ExclusionPolicy("all")
 * @author dao
 *
 */
class Escrow implements EscrowInterface
{
    use FiniteStateTrait;
    use CreatedAndUpdatedTrait;
    use IdentifiableTrait;
    use TermLoanTrait;

    const STATE_NEW = 'new';
    const STATE_PENDING = 'pending';
    const OPEN = 'open';
    const OPEN_CONFIRMED = 'open_confirmed';

    /**
     * @MongoDB\Date
     * @JMS\Expose
     * @var \DateTime
     */
    protected $start_date;

    /**
     * @MongoDB\Date
     * @JMS\Expose
     * @var \DateTime
     */
    protected $close_date;

    /**
     * @MongoDB\Int
     * @JMS\Expose
     * @var number
     */
    protected $funding_goal;

    /**
     * @MongoDB\Int
     * @JMS\Expose
     * @var number
     */
    protected $overfunding_amount;


    /** @MongoDB\Int
     * @JMS\Expose
     * @var number
     */
    protected $minimum_funding_amount;

    /**
     * @MongoDB\Int
     * @JMS\Expose
     * @var number
     */
    protected $maximum_funding_amount;

    /**
     * @MongoDB\Float
     * @JMS\Expose
     * @var number
     */
    protected $current_balance;

    /**
     * @MongoDB\Float
     * @JMS\Expose
     * @var number
     */
    protected $notional_balance;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\UserBundle\Document\User", simple="true")
     * @var User
     */
    protected $user;

    /**
     *
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\BorrowerBundle\Document\Business", simple=true)
     * @var Business
     */
    protected $business;

    /**
     * @MongoDB\ReferenceMany(targetDocument="StreetVC\LenderBundle\Document\LoanCommitment", simple=true, mappedBy="escrow")
     * @var ArrayCollection|LoanCommitment
     */
    protected $commitments;

    protected $options;

    /**
     * #MongoDB\ReferenceOne(targetDocument="StreetVC:Contract")
     * @var Contract
     */
    protected $contract;

    /**
     * @MongoDB\String
     * @JMS\Expose
     * @var string
     */
    protected $title;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LoanBundle\Document\LoanRequest", simple=true)
     * @var LoanRequest
     */
    protected $loan_request;

    /**
     * @MongoDB\ObjectId()
     * @var \MongoId
     * @JMS\Expose()
     */
    protected $loan_request_id;

    /**
     * @MongoDB\String
     * @JMS\Expose
     * @var string
     */
    protected $issuer_id;

    /**
     * @MongoDB\String
     * @JMS\Expose
     * @var string
     */
    protected $bancbox_id;

    /**
     * @var array
     * @MongoDB\Hash
     */
    protected $disbursement;

    /**
     * @var \DateTime
     * @MongoDB\Date
     * date on which escrow was disbursed to issuer
     */
    protected $disbursed_on;

    /**
     * @var float
     * @MongoDB\Float
     * amount allocated to platform for escrow fee
     */
    protected $platform_fee;

    /**
     * @var float
     * @MongoDB\Float
     * amount to disburse to issuer
     */
    protected $issuer_share;

    /**
     * @var \DateTime
     * @MongoDB\Date
     * date on which platform fee was collected
     */
    protected $platform_fee_collected;

    /**
     * @var RepaymentSchedule
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LoanBundle\Document\RepaymentSchedule", simple=true);
     */
    protected $repayment_schedule;

    /**
     * @var bool
     * @MongoDB\Boolean()
     * @JMS\Expose()
     */
    protected $funded;

    /**
     * @var bool
     * @MongoDB\Boolean()
     * @JMS\Expose()
     */
    protected $expired;

    /**
     * @var int
     * @MongoDB\Int
     * @JMS\Expose
     */
    protected $percent_funded;

    private function _init()
    {
        $this->id = (string) new \MongoId();
        $this->created = new \DateTime();
        $this->commitments = new ArrayCollection();
        $this->state = self::STATE_NEW;
        $this->notional_balance = 0;
        $this->current_balance = 0;
        $this->funded = false;
        $this->percent_funded = 0;
        $this->expired = false;
    }

    public function __construct()
    {
        $this->_init();
    }

    public function __clone()
    {
        $this->_init();
    }

    public function setFiniteState($state)
    {
        $this->state = $state;
        $this->getLoanRequest()->setEscrowState($state);
    }

    /**
     * @param $id
     * @return $this
     */
    public function setBancboxId($id)
    {
        $this->bancbox_id = $id;
        $this->getLoanRequest()->setBancboxId($id);
        return $this;
    }

    /**
     * @return string
     * get bancbox id of the issuer
     */
    public function getIssuerId()
    {
        return $this->issuer_id;
    }

    public function getBancboxId()
    {
        return $this->bancbox_id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getBusiness()
    {
        if (!$this->getLoanRequest()) {
            return null;
        }
        return $this->getLoanRequest()->getBusiness();
    }

    public function getTitle()
    {
        return $this->title ?: $this->getLoanRequest()->getTitle();
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return LoanRequest
     */
    public function getLoanRequest()
    {
        return $this->loan_request;
    }
    public function getLoanRequestId()
    {
        return $this->loan_request_id;
    }

    public function setLoanRequest(LoanRequest $loanRequest)
    {
        if ($this->loan_request || $this->loan_request_id) {
            throw new \Exception("Loan Request already set on escrow");
        }
        $this->loan_request = $loanRequest;
        $this->funding_goal = $loanRequest->getFundingGoal();
        $this->loan_request_id = $loanRequest->getId();
        $this->term_payment = $loanRequest->getTermPayment();
        $this->title = $loanRequest->getTitle();
        $this->user = $loanRequest->getUser();
        $this->business = $loanRequest->getBusiness();
        $this->issuer_id = $loanRequest->getIssuerId();
        $this->close_date = $loanRequest->getCloseDate();
        $this->start_date = $loanRequest->getStartDate();
    }

    public function getContract()
    {
        return $this->contract;
    }

    public function setContract(Contract $contract)
    {
        $this->contract = $contract;
    }

    public function getFundingGoal()
    {
        return $this->funding_goal;
    }

    public function isFullyFunded()
    {
        return $this->funded;
    }

    public function checkFullyFunded()
    {
        $this->funded = $this->getAmountFunded() >= $this->getFundingGoal();
    }

    public function getOverfundingAmount()
    {
        return $this->overfunding_amount;
    }

    public function setOverfundingAmount($amount)
    {
        $this->overfunding_amount = $amount;
        return $this;
    }

    public function getMinimumFundingAmount()
    {
        return $this->minimum_funding_amount ?: 250;
    }

    public function setMinimumFundingAmount($amount)
    {
        $this->minimum_funding_amount = $amount;
        return $this;
    }

    /**
     * Set maximum contribution amount
     *
     * @param int $amount
     * @return self
     */
    public function setMaximumFundingAmount($amount)
    {
        $this->maximum_funding_amount = $amount;
        return $this;
    }

    public function getMaximumFundingAmount()
    {
        return $this->maximum_funding_amount;
    }

    protected function calculateMaximumAmount()
    {
        if (!$this->funding_goal) {
            throw new \Exception("Can't get minimum amount without goal");
        }
        $maximum = $this->funding_goal ? $this->funding_goal / 10 : 0;
        return $maximum;
    }

    /**
     * @return ArrayCollection|LoanCommitment
     */
    public function getCommitments()
    {
        return $this->commitments;
    }

    public function getValidCommitments()
    {
        return $this->getCommitments()->filter(function(LoanCommitment $commitment) {
            return $commitment->isValid();
        });
    }

    /**
     * Add commitment
     *
     * @param LoanCommitmentInterface $commitment
     * @throws \InvalidArgumentException
     */
    public function addCommitment(LoanCommitmentInterface $commitment)
    {
        $this->checkCommitment($commitment);
        $this->notional_balance += $commitment->getAmount();
        $this->current_balance += $commitment->getAmount();
        $this->updateProgress();
    }

    protected function checkCommitment(LoanCommitmentInterface $commitment)
    {
        if ($commitment->getEscrow() !== $this) {
            throw new \InvalidArgumentException("Attempt to assign loan commitment " . $commitment->getId() . "to the wrong escrow (" . $this->getId() .")");
        }
        return true;
    }

    public function updateProgress()
    {
        $this->checkFullyFunded();
        if (!$goal = $this->getFundingGoal()) {
            throw new \Exception("Can't update progress without funding goal.");
        }
        $funded = $this->getAmountFunded();
        $this->percent_funded = ($funded / $goal) * 100;
    }

    public function getPercentFunded()
    {
        return $this->percent_funded;
    }

    /**
     * @return mixed
     * @deprecated use getPercentFunded
     */
    public function getFundingProgress()
    {
        $progress = $this->getAmountFunded() / $this->getFundingGoal();
        return $progress;
    }

    public function getAmountFunded()
    {
        return $this->notional_balance;
    }

    public function getMaximumCommitment()
    {
        $available = $this->getAmountRemaining();
        $maximum = $this->getMaximumFundingAmount();
        return $available < $maximum ? $available : $maximum;
    }

    /**
     * @return int|number
     * @todo allow for overfunding limit
     */
    public function getAmountRemaining()
    {
        $funded = $this->getAmountFunded();
        $goal = $this->getFundingGoal();
        return $goal - $funded;
    }

    /**
     * Set currentBalance
     *
     * @param float $currentBalance
     * @return self
     */
    public function setCurrentBalance($currentBalance)
    {
        $this->current_balance = $currentBalance;
        return $this;
    }

    /**
     * Get currentBalance
     *
     * @return float $currentBalance
     */
    public function getCurrentBalance()
    {
        return $this->current_balance;
    }

    /**
     * Set notionalBalance
     *
     * @param float $notionalBalance
     * @return self
     */
    public function setNotionalBalance($notionalBalance)
    {
        $this->notional_balance = $notionalBalance;
    }

    /**
     * Get notionalBalance
     *
     * @return float $notionalBalance
     */
    public function getNotionalBalance()
    {
        return $this->notional_balance;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return self
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->start_date = $startDate;
        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime $startDate
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set closeDate
     *
     * @param \DateTime $closeDate
     * @return self
     */
    public function setCloseDate(\DateTime $closeDate)
    {
        $this->close_date = $closeDate;
        return $this;
    }

    /**
     * Get closeDate
     *
     * @return \DateTime $closeDate
     */
    public function getCloseDate()
    {
        return $this->close_date;
    }

    public function setPlatformFee($amount)
    {
        $this->platform_fee = $amount;
    }

    public function getPlatformFee()
    {
        return $this->platform_fee;
    }

    public function getPlatformFeeCollected()
    {
        return $this->platform_fee_collected;
    }

    public function setPlatformFeeCollected()
    {
        $this->platform_fee_collected = new \DateTime();
    }

    public function getIssuerShare()
    {
        return $this->issuer_share;
    }

    public function setIssuerShare($amount)
    {
        $this->issuer_share = $amount;
    }

    public function disburse()
    {
        $this->disbursed_on = new \DateTime();
        /** @todo set balance via transaction */
        $this->getBusiness()->addFunds($this->getIssuerShare()); // @todo do this as ledger entry
//        $this->withdrawFunds($disbursement->getTotal());
        $this->setCurrentBalance(0);
        /** @var LoanCommitment $commitment */
        foreach ($this->getCommitments() as $commitment) {
            $commitment->disburse();
        }
    }

    public function setDisbursement($disbursement)
    {
        $this->disbursement = $disbursement;
    }

    public function getDisbursement()
    {
        return $this->disbursement;
    }

    public function getDisbursedOn()
    {
        return $this->disbursed_on;
    }

    /**
     * @return RepaymentSchedule
     */
    public function getRepaymentSchedule()
    {
        return $this->repayment_schedule;
    }

    public function setRepaymentSchedule(RepaymentSchedule $schedule)
    {
        $this->repayment_schedule = $schedule;
    }

    public function isExpired()
    {
        return $this->expired;
    }

    public function setExpired()
    {
        if (!$this->getCloseDate() < new \DateTime()) {
            throw new \Exception("Shouldn't expire a loan before its close date has passed.");
        }
        $this->expired = true;
    }
}
