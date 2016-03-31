<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 9/30/2014
 * Time: 2:08 PM
 */

namespace StreetVC\LoanBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use StreetVC\LenderBundle\Document\Lender;

/**
 * Class RepaymentShare
 * @package StreetVC\LoanBundle\Document
 * @MongoDB\EmbeddedDocument()
 */
class RepaymentShare {

    /**
     * @var string
     * @MongoDB\String
     * @JMS\Expose
     */
    protected $investor_id;

    /**
     * @var float
     * @MongoDB\Float
     * @JMS\Expose
     */
    protected $amount;

    /**
     * @var string
     * @MongoDB\ObjectId()
     * @JMS\Expose
     */
    protected $lender_id;

    /**
     * @var string
     * @MongoDB\String
     * @JMS\Expose
     * id for this schedule item assigned by bancbox
     */
    protected $schedule_id;

    public function __construct(Lender $lender, $amount)
    {
        $this->amount = $amount;
        $this->investor_id = $lender->getBancboxId();
        $this->lender_id = $lender->getId();
    }

    public function getScheduleId()
    {
        return $this->schedule_id;
    }

    public function setScheduleId($schedule_id)
    {
        $this->schedule_id = $schedule_id;
    }

    /**
     * @return string
     */
    public function getInvestorId()
    {
        return $this->investor_id;
    }

    /**
     * @param string $investor_id
     */
    public function setInvestorId($investor_id)
    {
        $this->investor_id = $investor_id;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getLenderId()
    {
        return $this->lender_id;
    }

    /**
     * @param string $lender_id
     */
    public function setLenderId($lender_id)
    {
        $this->lender_id = $lender_id;
    }

}
