<?php
namespace StreetVC\LoanBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use StreetVC\BaseBundle\Document\Contract;
use StreetVC\BaseBundle\Document\Contract\OpenEscrowContract;
use StreetVC\BorrowerBundle\Document\Business;
use StreetVC\LoanEvaluation\Bundle\Document\LoanEvaluation;
use StreetVC\LoanEvaluation\Model\LoanEvaluationInterface;
use StreetVC\LoanEvaluation\Model\LoanRequestInterface;
use StreetVC\UserBundle\Document\User;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 * @JMS\ExclusionPolicy("all")
 */
class LoanRequest implements LoanRequestInterface
{
    use \StreetVC\TransactionBundle\Traits\FiniteStateTrait;
    use \Sd\BaseBundle\Has\CreatedAndUpdatedTrait;
    use \Sd\BaseBundle\Has\IdentifiableTrait;
    use \StreetVC\LoanBundle\Model\Traits\TermLoanTrait;

    const STATE_DRAFT = 'draft';
    const TRANSITION_ACCEPT = 'accept';
//    const DRAFT = 'draft', PROPOSED = 'proposed', ACCEPTED = 'accepted', REFUSED = 'refused';

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\BorrowerBundle\Document\Business", simple=true, inversedBy="loan_requests")
     * @JMS\Expose
     * #Assert\NotNull
     * @var Business
     */
    protected $business;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $bancbox_id;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $issuer_id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LoanBundle\Document\Escrow", simple=true)
     * @var Escrow
     */
    protected $escrow;

    /**
     * @MongoDB\ObjectId
     * @var \MongoId
     */
    protected $escrow_id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LoanBundle\Document\Loan", simple=true)
     * @var Loan
     */
    protected $loan;

    /**
     * @MongoDB\ObjectId
     * @var \MongoId
     */
    protected $loan_id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\UserBundle\Document\User", simple=true, inversedBy="loan_requests")
     * #Assert\NotNull
     * @var User
     */
    protected $user;

    /**
     * @MongoDB\Int
     * @Assert\NotNull
     * @Assert\GreaterThan(0)
     * @JMS\Expose
     */
    protected $funding_goal;

    /**
     * @MongoDB\Date
     * #Assert\NotNull
     * @JMS\Expose
     * @var \DateTime
     */
    protected $start_date;

    /**
     * @MongoDB\Date
     * #Assert\NotNull
     * @JMS\Expose
     * @var \DateTime
     */
    protected $close_date;

    /**
     * @MongoDB\String
     * @JMS\Expose
     * @var string
     */
    protected $created_by;

    /**
     * @MongoDB\Int
     * @JMS\Expose
     * @var int
     */
    protected $overfunding_amount = 0;

    /**
     * @MongoDB\String
     * @Assert\NotNull
     * @Assert\NotBlank
     * @var string
     */
    protected $title;

    /**
     * #MongoDB\ReferenceOne(targetDocument="StreetVC\BaseBundle\Document\Contract", simple=true)
     * @MongoDB\ReferenceOne(targetDocument="\Base:Contract", simple=true)
     * @var Contract
     */
    protected $contract;

    /**
     * anticipated time to utilize the loan in months
     * @MongoDB\Int
     * #Assert\GreaterThan(0)
     * @JMS\Expose
     */
    protected $time_to_utilize = 0;

    /**
     * @JMS\Expose
     * @MongoDB\Int
     * projected increase in revenue from utilization of this loan
     */
    protected $additional_revenue = 0;

    /**
     * @MongoDB\Int
     * @var integer
     * User-supplied FICO score
     */
    protected $fico_score;

    /**
     * @MongoDB\Date
     * @var \DateTime
     */
    protected $submitted_on;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LoanEvaluation\Bundle\Document\LoanEvaluation", simple=true)
     * @var LoanEvaluation
     */
    protected $evaluation;

    /**
     * @MongoDB\String
     * intro video on why lenders should contribute $ to initiative
     */
    protected $video;

    /**
     * @var string
     * @MongoDB\String
     */
    protected $escrow_state;
    
    /**
     * @var string
     * @MongoDB\String
     */
    protected $use_of_loan_proceeds;
    
    /**
     * @var string
     * @MongoDB\String
     */
    protected $description;


    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        $this->id = (string) new \MongoId();
        $this->created = new \DateTime();
        $this->state = self::STATE_DRAFT;
    }

    public function __clone()
    {
        $this->_init();
    }

    public function getEscrowState()
    {
        return $this->escrow_state;
    }

    public function setEscrowState($state)
    {
        $this->escrow_state = $state;
    }

    public function setEvaluation(LoanEvaluationInterface $evaluation)
    {
        $this->evaluation = $evaluation;
        $this->setInterestRate($evaluation->getRate());
    }

    public function getEvaluation()
    {
        return $this->evaluation;
    }

    public function isOpen()
    {
        return $this->getEscrowState() == 'open' && $this->start_date < new \DateTime() && $this->close_date > new \DateTime();
    }

    public function isSubmitted()
    {
        return $this->submitted_on instanceof \DateTime;
    }

    public function getIssuerId()
    {
        return $this->issuer_id;
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
     * @param \DateTime $startDate
     * @return $this
     * Date requested for start of funding
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->start_date = $startDate;
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

    public function timeUntilClose()
    {
        $today = new \DateTime();
        $close = $this->getCloseDate();
        if($close < $today){
          return false;
        }else{
          return $close->diff($today)->days;
        }
    }

    public function isValid($soft = true)
    {
        $violations = $this->validate();
        return $violations->count() === 0;
    }

    public function validate()
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $violations = $validator->validate($this);
        return $violations;
    }

    /**
     * @return float|int
     * @throws \Exception
     * allow escrow to be fully funded by single investor
     */
    public function getMaximumFundingAmount()
    {
        if (!$goal = $this->getFundingGoal()) {
            throw new \Exception("Funding goal not set");
        }
        return $this->getFundingGoal();
    }

    public function getFundingGoal()
    {
        return $this->funding_goal;
    }

    /**
     * Set fundingGoal
     *
     * @param int $fundingGoal
     * @return self
     */
    public function setFundingGoal($fundingGoal)
    {
        $this->funding_goal = $fundingGoal;
        $this->overfunding_amount = $fundingGoal;
        return $this;
    }

    /**
     * @return int
     * @throws \Exception
     * @todo set minimum funding amount via system config / factor of funding goal
     * for now allow any minimum
     */
    public function getMinimumFundingAmount()
    {
        return 1;
    }

    public function isEditable()
    {
        return $this->state == 'draft';
    }

    public function getAcceptedOn()
    {
        if (!$this->evaluation) {
            return null;
        }
        return $this->evaluation->getAcceptedOn();
    }

    public function isFinalized()
    {
        return $this->getState() == 'accepted';
    }

    public function getBancboxId()
    {
        return $this->bancbox_id;
    }

    public function setBancboxId($id)
    {
        $this->bancbox_id = $id;
    }

    public function getEscrowId()
    {
        return $this->escrow_id;
    }

    public function isEscrow()
    {
        return $this->escrow !== null && $this->loan == null;
    }

    /**
     * @return Escrow
     */
    public function getEscrow()
    {
        return $this->escrow;
    }

    /**
     * @return Escrow
     * @throws \Exception
     */
    public function createEscrow()
    {
        if ($this->escrow || $this->escrow_id) {
            throw new \Exception("Escrow already created.");
        }

        $escrow = new Escrow();
        $escrow->setLoanRequest($this);
        $escrow->setTitle($this->getTitle());
        $escrow->setTerm($this->getTerm());
        $escrow->setInterestRate($this->getInterestRate());
        $escrow->setStartDate($this->getStartDate());
        $escrow->setCloseDate($this->getCloseDate());
        $escrow->setMinimumFundingAmount($this->getMinimumFundingAmount());
        $escrow->setMaximumFundingAmount($this->getMaximumFundingAmount());
        $escrow->setBancboxId($this->getBancboxId());
        $this->escrow = $escrow;
        $this->escrow_id = $escrow->getId();
        return $escrow;
    }

    public function isLoan()
    {
        return $this->loan instanceof Loan;
    }

    /**
     * @return Loan
     */
    public function getLoan()
    {
        return $this->loan;
    }

    public function setLoan(Loan $loan)
    {
        $this->loan = $loan;
    }

    // @todo ensure that submitted_on is set on submission
    public function getSubmittedOn()
    {
        return $this->submitted_on;
    }

    public function submit()
    {
        if ($this->submitted_on) {
            throw new \Exception("Loan Request already submitted");
        }
        $this->submitted_on = new \DateTime();
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->created_by = $user->getFullName();
        return $this;
    }

    /**
     * @return Business
     */
    public function getBusiness()
    {
        return $this->business;
    }

    public function setBusiness(Business $business)
    {
        $this->business = $business;
        $this->issuer_id = $business->getBancboxId();
        return $this;
    }

    public function getAdditionalRevenue()
    {
        return $this->additional_revenue;
    }

    public function setAdditionalRevenue($revenue)
    {
        $this->additional_revenue = $revenue;
        return $this;
    }

    public function getTimeToUtilize()
    {
        return $this->time_to_utilize;
    }

    public function setTimeToUtilize($time)
    {
        $this->time_to_utilize = $time;
        return $this;
    }

    /**
     * @return OpenEscrowContract
     */
    public function getContract()
    {
        return $this->contract;
    }

    public function setContract(Contract $contract)
    {
        $this->contract = $contract;
        return $this;
    }

    public function isContractSigned()
    {
        if (!$this->contract) {
            return;
        }
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

    /**
     * @return Business
     */
    public function getIssuer()
    {
        return $this->getBusiness();
    }



    /**
     * Set video
     *
     * @param string $video
     * @return self
     */
    public function setVideo($video)
    {
        $this->video = $video;
        return $this;
    }

    /**
     * Get video
     *
     * @return string $video
     */
    public function getVideo()
    {
        return $this->video;
    }
    

    public function setUseOfLoanProceeds($use_of_loan_proceeds)
    {
        $this->use_of_loan_proceeds = $use_of_loan_proceeds;
        return $this;
    }

    public function getUseOfLoanProceeds()
    {
        return $this->use_of_loan_proceeds;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }

}
