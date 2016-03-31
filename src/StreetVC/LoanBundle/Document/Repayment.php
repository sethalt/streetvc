<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 9/15/2014
 * Time: 12:22 PM
 */

namespace StreetVC\LoanBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use StreetVC\TransactionBundle\Traits\FiniteStateTrait;

/**
 * Class Repayment
 * @package StreetVC\LoanBundle\Document
 * @MongoDB\EmbeddedDocument
 */
class Repayment {

    use FiniteStateTrait;

    /**
     * @var \MongoId
     * @MongoDB\Id(strategy="NONE")
     */
    protected $id;

    /**
     * @var \DateTime
     * @MongoDB\Date
     * @JMS\Expose()
     */
    protected $date;

    /**
     * @var float
     * @MongoDB\Float
     * @JMS\Expose()
     */
    protected $amount;

    /**
     * @var array
     * @MongoDB\EmbedMany(targetDocument="RepaymentShare")
     * @JMS\Expose
     */
    protected $shares;

    /**
     * @var string
     * @MongoDB\String
     * @JMS\Expose()
     */
    protected $proceeds_batch_id;

    /**
     * @var string
     * @MongoDB\String
     * @JMS\Expose()
     */
    protected $request_id;

    /**
     * @var array
     * @MongoDB\Hash
     */
    protected $request;

    /**
     * @var array
     * @MongoDB\Hash
     */
    protected $response;

    public function __construct()
    {
        $this->id = new \MongoId();
        $this->request = [];
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getRequestId()
    {
        return $this->request_id;
    }

    public function setRequestId($id)
    {
        $this->request_id = $id;
    }

    public function getProceedsBatchId()
    {
        return $this->proceeds_batch_id;
    }

    public function setProceedsBatchId($id)
    {
        $this->proceeds_batch_id = $id;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setShares($shares)
    {
        $this->shares = $shares;
    }

    /**
     * @return array|RepaymentShare
     */
    public function getShares()
    {
        return $this->shares;
    }

    /**
     * @param $investor_id
     * @return RepaymentShare
     */
    public function getShareByInvestorId($investor_id)
    {
        /** @var RepaymentShare $share */
        foreach ($this->getShares() as $share) {
            if ($share->getInvestorId() == $investor_id) {
                return $share;
            }
        }
    }

}
