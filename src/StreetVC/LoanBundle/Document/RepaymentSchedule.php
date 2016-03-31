<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 9/15/2014
 * Time: 12:10 PM
 */

namespace StreetVC\LoanBundle\Document;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use Sd\BaseBundle\Has\CreatedAndUpdatedTrait;
use StreetVC\BorrowerBundle\Document\Business;


/**
 * Class RepaymentSchedule
 * @package StreetVC\LoanBundle\Document
 * @MongoDB\Document()
 * @JMS\ExclusionPolicy("all")
 */
class RepaymentSchedule
{

    use CreatedAndUpdatedTrait;

    /**
     * @var string
     * @MongoDB\Id(strategy="NONE")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @var string
     * @MongoDB\String
     * @JMS\Expose()
     * bancbox issuer id
     */
    protected $issuer_id;

    /**
     * @var string
     * @MongoDB\String
     * @JMS\Expose()
     * bancbox escrow id
     */
    protected $escrow_id;

    /**
     * @var Escrow
     * @MongoDB\ReferenceOne(targetDocument="\StreetVC\LoanBundle\Document\Escrow", simple=true)
     * @JMS\Expose
     */
    protected $escrow;

    /**
     * @var Business
     * @MongoDB\ReferenceOne(targetDocument="\StreetVC\BorrowerBundle\Document\Business")
     * @JMS\Expose
     */
    protected $business;

    /**
     * @var array|ArrayCollection
     * @MongoDB\EmbedMany(targetDocument="Repayment")
     * @JMS\Expose()
     */
    protected $repayments;

    /**
     * @var int
     * @MongoDB\Int
     * @JMS\Expose()
     */
    protected $count;

    /**
     * @var float
     * @MongoDB\Float
     * @JMS\Expose
     */
    protected $term_payment;

   /**
     * @var ArrayCollection|RepaymentShare
     * @MongoDB\EmbedMany(targetDocument="RepaymentShare")
     * @JMS\Expose
     */
    protected $shares;

    public function __construct(Escrow $escrow)
    {
        $this->id = new \MongoId();
        $this->created = new \DateTime();
        $this->setEscrow($escrow);
        $this->setTermPayment($escrow->getTermPayment());
        $this->count = $escrow->getTerm();
        $this->setBusiness($escrow->getBusiness());
        $this->repayments = new ArrayCollection();
        $this->shares = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getTermPayment()
    {
        return $this->term_payment;
    }

    /**
     * @param float $term_payment
     */
    public function setTermPayment($term_payment)
    {
        $this->term_payment = $term_payment;
    }


    public function getCount()
    {
        return $this->count;
    }

    public function getShares()
    {
        return $this->shares;
    }

    public function setShares($shares)
    {
        $this->shares = $shares;
    }

    private function setBusiness(Business $business)
    {
        $this->business = $business;
        $this->issuer_id = $business->getBancboxId();
        if (!$this->issuer_id) {
            throw new \Exception("No issuer bancbox id provided");
        }
    }

    /**
     * @return Business
     */
    public function getBusiness()
    {
        return $this->business;
    }

    private function setEscrow(Escrow $escrow)
    {
        $this->escrow = $escrow;
        $this->escrow_id = $escrow->getBancboxId();
        if (!$this->escrow_id) {
            throw new \Exception("No escrow bancbox id provided");
        }
    }

    /**
     * @return Escrow
     */
    public function getEscrow()
    {
        return $this->escrow;
    }

    public function getIssuerId()
    {
        return $this->issuer_id;
    }

    public function getEscrowId()
    {
        return $this->escrow_id;
    }

    /**
     * @return Repayment|ArrayCollection
     */
    public function getRepayments()
    {
        return $this->repayments;
    }

    public function addRepayment(Repayment $repayment)
    {
        $this->repayments->add($repayment);
    }

}
