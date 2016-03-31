<?php
namespace StreetVC\LenderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use StreetVC\LenderBundle\Model\LoanCommitmentInterface;
use StreetVC\BaseBundle\Document\Contract;
use StreetVC\TransactionBundle\Traits\FiniteStateTrait;
use Symfony\Component\Validator\Constraints as Assert;
use StreetVC\UserBundle\Document\User;
use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\TransactionBundle\Document\BankAccount;
use StreetVC\LoanBundle\Document\Escrow;

/**
 * @MongoDB\Document
 * @JMS\ExclusionPolicy("all")
 */
class LoanCommitment implements LoanCommitmentInterface
{
    use \Sd\BaseBundle\Has\CreatedAndUpdatedTrait;
    use \Sd\BaseBundle\Has\IdentifiableTrait;
    use FiniteStateTrait;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\UserBundle\Document\User", simple=true, inversedBy="commitments")
     * @var User
     */
    protected $user;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LenderBundle\Document\Lender", simple=true, inversedBy="commitments")
     * @var Lender
     */
    protected $lender;

    /**
     * @var \MongoId
     * @MongoDB\ObjectId()
     * @JMS\Expose
     */
    protected $user_id;

    /**
     * @var \MongoId
     * @MongoDB\ObjectId()
     * @JMS\Expose
     */
    protected $lender_id;

    /**
     * @var \MongoId
     * @MongoDB\ObjectId()
     * @JMS\Expose
     */
    protected $escrow_id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LoanBundle\Document\Escrow", simple=true, inversedBy="commitments")
     * @var Escrow
     */
    protected $escrow;

    /**
     * @MongoDB\String
     * @JMS\Expose
     * @var string
     */
    protected $bancbox_escrow_id;

    /**
     * @MongoDB\String
     * @JMS\Expose
     * @var string
     */
    protected $bancbox_investor_id;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $client_ip_address;

    /**
     * @MongoDB\Int
     * @JMS\Expose
     * @var number
     **/
    protected $amount;

    /**
     * @MongoDB\Hash
     * @var array
     */
    protected $response;

    /**
     * @MongoDB\String
     * @JMS\Expose
     * @var string
     */
    protected $title;

    /**
     * @MongoDB\Boolean()
     * @JMS\Expose
     * @var boolean
     */
    protected $valid;

    /**
     * @MongoDB\Boolean()
     * @JMS\Expose
     * @var boolean
     */
    protected $refunded;

    /**
     * @var boolean
     * @MongoDB\Boolean()
     * @JMS\Expose
     */
    protected $disbursed;

    /**
     * @MongoDB\ReferenceOne(targetDocument="\Base:Contract")
     */
    protected $contract;

    public function __construct(Escrow $escrow)
    {
        $this->id = (string) new \MongoId();
        $this->created = new \DateTime();
        $this->title = $escrow->getTitle();
        $this->bancbox_escrow_id = $escrow->getBancboxId();
        $this->escrow = $escrow;
        $this->escrow_id = $escrow->getId();

        $this->valid = true;
        $this->setFiniteState('committed');
    }

    public function getShare()
    {
        return $this->amount / $this->getEscrow()->getFundingGoal();
    }

    public function getPercentage()
    {
        return $this->getShare() * 100;
    }

    public function setLender(Lender $lender)
    {
        $this->lender = $lender;
        $this->user = $lender->getUser();
        $this->client_ip_address = $this->user->getLastIp();
        $this->bancbox_investor_id = $lender->getBancboxId();
        $this->lender_id = $lender->getId();
        $this->user_id = $lender->getUserId();
    }

    public function disburse()
    {
        $this->setFiniteState('disbursed');
        $this->disbursed = true;
    }

    public function getDisbursed()
    {
        return $this->disbursed;
    }

    public function refund()
    {
        $this->valid = false;
        $this->setFiniteState('refunded');
        $this->refunded = true;
    }

    public function getRefunded()
    {
        return $this->refunded;
    }

    public function getStates()
    {
        return ['committed', 'disbursed', 'refunded', 'failed' ];
    }

    public function setAmount($amount)
    {
        if (!$amount >0) {
            throw new \Exception("Invalid Amount");
        }
        $this->amount = $amount;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function isValid()
    {
        return $this->valid;
    }

    public function setValid()
    {
        $this->valid = true;
    }

    public function setInvalid()
    {
        $this->valid = false;
    }

    public function getLoanRequest()
    {
        return $this->getEscrow()->getLoanRequest();
    }

    public function getLoanRequestId()
    {
        return $this->getEscrow()->getLoanRequest()->getId();
    }

    public function getTitle()
    {
        return $this->title ?: '';
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \StreetVC\UserBundle\Document\User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getClientIpAddress()
    {
        return $this->client_ip_address;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getLender()
    {
        return $this->lender;
    }

    public function getInvestorId()
    {
        return $this->bancbox_investor_id;
    }

    /**
     * @return \StreetVC\LoanBundle\Document\Escrow
     */
    public function getEscrow()
    {
        return $this->escrow;
    }

    /**
     * @return string
     */
    public function getBancboxInvestorId()
    {
        return $this->bancbox_investor_id;
    }

    /**
     * @return string
     */
    public function getBancboxEscrowId()
    {
        return $this->bancbox_escrow_id;
    }

}
