<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/18/14
 * Time: 5:27 PM
 */

namespace StreetVC\LoanBundle\Document;


use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class Disbursement
 * @package StreetVC\LoanBundle\Document
 * @MongoDB\Document
 */
class Disbursement extends \StreetVC\Model\Disbursement {

    /**
     * @MongoDB\Id
     * @var \MongoId
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LoanBundle\Document\Escrow", simple=true)
     * @var Escrow
     */
    protected $escrow;

    /**
     * @var string
     * @MongoDB\String
     */
    protected $escrow_id;

    /**
     * @MongoDB\Date()
     * @var \DateTime
     */
    protected $disbursed_on;

    /**
     * @var string
     * @MongoDB\String
     * id assigned by bancbox
     */
    protected $bancbox_id;

    /**
     * @MongoDB\Float
     * @var float
     */
    protected $amount;

    public function __construct()
    {
        $this->id = new \MongoId();
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setDisbursedOn(\DateTime $dateTime)
    {
        $this->disbursed_on = $dateTime;
    }

    /**
     * @return \DateTime
     */
    public function getDisbursedOn()
    {
        return $this->disbursed_on;
    }

    public function getBancboxId()
    {
        return $this->bancbox_id;
    }

    public function setBancboxId($bancbox_id)
    {
        $this->bancbox_id = $bancbox_id;
    }

    /**
     * @return \MongoId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Escrow
     */
    public function getEscrow()
    {
        return $this->escrow;
    }

    /**
     * @param Escrow $escrow
     */
    public function setEscrow(Escrow $escrow)
    {
        $this->escrow = $escrow;
    }

    /**
     * @return string
     */
    public function getEscrowId()
    {
        return $this->escrow_id;
    }

    /**
     * @param string $escrow_id
     */
    public function setEscrowId($escrow_id)
    {
        $this->escrow_id = $escrow_id;
    }


}
