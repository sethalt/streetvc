<?php
namespace StreetVC\LenderBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use Sd\BaseBundle\Has\CreatedAndUpdatedTrait;
use Sd\BaseBundle\Has\IdentifiableTrait;
use StreetVC\BancboxInvest\BancboxInvestBundle\Traits\BancboxAccountTrait;
use StreetVC\LenderBundle\Model\LoanCommitmentInterface;
use StreetVC\UserBundle\Document\User;
use StreetVC\TransactionBundle\Document\BankAccount;
use StreetVC\LoanBundle\Document\Escrow;

/**
 * @MongoDB\Document
 * @JMS\ExclusionPolicy("all")
 */
class Lender
{
    use BancboxAccountTrait;
    use CreatedAndUpdatedTrait;
    use IdentifiableTrait;

    /**
     * @MongoDB\Int
     * @var int
     */
    protected $funds;

    /**
     * @MongoDB\Boolean
     * @JMS\Expose
     * @var boolean
     */
    protected $accredited;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\UserBundle\Document\User", simple=true)
     * @var User
     */
    protected $user;

    /**
     * @var \MongoId
     * @MongoDB\ObjectId()
     * @JMS\Expose()
     */
    protected $user_id;

    /**
     * @MongoDB\String
     * @JMS\Expose
     * @var string
     */
    protected $name;

    protected $loans;

    /**
     * @MongoDB\ReferenceMany(targetDocument="StreetVC\LenderBundle\Document\LoanCommitment", simple=true, mappedBy="lender")
     * @var ArrayCollection|LoanCommitmentInterface
     */
    protected $commitments;

    /**
     * @MongoDB\Collection
     * @JMS\Expose
     * @var array Ids of all escrows funded
     */
    protected $escrows_funded;

    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->user = $user;
        $this->loans = new ArrayCollection();
        $this->internal_account = new BankAccount();
        $this->linked_accounts = new ArrayCollection();
        $this->created = new \DateTime();
        $this->commitments = new ArrayCollection();
        $this->escrows_funded = [];
        $this->current_balance = 0;
        $this->pending_balance = 0;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAccredited()
    {
        return $this->accredited;
    }

    public function setAccredited($bool)
    {
        $this->accredited = $bool;
        return $this;
    }

    public function getFunds()
    {
        return $this->funds;
    }

    public function setFunds($funds)
    {
        $this->funds = $funds;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->user_id = $user->getId();
        $this->name = $user->getFullName();
    }

    public function getLoans()
    {
        return $this->loans;
    }

    public function getCommitments()
    {
        return $this->commitments;
    }

    public function hasCommittedToEscrow(Escrow $escrow)
    {
        return $this->getCommitmentToEscrow($escrow) != null;
    }

    public function getCommitmentToEscrow(Escrow $escrow)
    {
        foreach($this->commitments as $commitment){
            if ($commitment->getEscrow()->getId() == $escrow->getId()) {
                return $escrow;
            }
        }
        return null;
    }

    public function getFundsInEscrow()
    {
        $amount = 0;
        foreach ($this->getCommitments() as $commitment) {
            if (!$commitment->getEscrow()->isFullyFunded()) {
                $amount += $commitment->getAmount();
            }
        }
        return $amount;
    }

    public function getAmountInvested()
    {
        $amount = 0;
        foreach ($this->getCommitments() as $commitment) {
            if ($commitment->getEscrow()->isFullyFunded()) {
                $amount += $commitment->getAmount();
            }
        }
        return $amount;
    }

    public function getCommittedAmount()
    {
        $amount = 0;
        foreach ($this->getCommitments() as $commitment) {
            if (!$commitment->getEscrow()->isFullyFunded()) {
                $amount += $commitment->getAmount();
            }
        }
        return $amount;
    }

    /*
    public function commitFundsToEscrow(Escrow $escrow, $amount)
    {
        $balance = $this->getCurrentBalance();
        if (!$amount <= $balance) {
            throw new \Exception("Lender has insufficient funds to commit to escrow.");
        }
        if ($amount > $escrow->getAmountRemaining()) {
            throw new \Exception("Loan commitment is greater than amount remaining in escrow.");
        }
        $commitment = new LoanCommitment($escrow);

        return $commitment;
    }
    */

}
