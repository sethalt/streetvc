<?php
namespace StreetVC\TransactionBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use StreetVC\BorrowerBundle\Document\Business;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Withdrawal
{

    /**
     * @MongoDB\Id
     * @var \MongoId
     */
    private $id;

    /**
     * @MongoDB\Float
     * @var int amount of withdrawal
     */
    private $amount;

    /**
     * @MongoDB\String
     * @var string method of withdrawal ('ACH' or 'CHECK')
     */
    private $method;

    /**
     * @MongoDB\String
     * @var string status of withdrawal ('fail' or 'success')
     */
    private $status;

    /**
     * @MongoDB\Date()
     * @var \DateTime
     */
    private $created;

   /**
     * @MongoDB\String
     * @var string
     */
    private $text;

    /**
     * @MongoDB\String
     * @var string
     */
    private $memo;

    /**
     * @MongoDB\ObjectId()
     * @var \MongoId
     */
    private $user_id;

    /**
     * @MongoDB\String
     * @var string
     */
    private $event_id;

    /**
     * @MongoDB\String
     * @var string
     */
    private $request_id;

    /**
     * @MongoDB\String
     * @var string
     */
    private $client_ip_address;

    public function __construct(BancboxEntityBusiness $business, $account, $amount, $memo = '')
    {
        $user = $business->getUser();
        $this->id = new \MongoId();
        $this->business = $business;
        $this->issuer_id = $business->getBancboxId();
        $this->user_id = $user->getId();
        $this->client_ip_address = $user->getLastIp();
        $this->amount = $amount;
        $this->method = 'ACH';
        $this->created = new \DateTime();
        $this->text = "I authorize Bancbox to make this transaction.";
        $this->memo = $memo;
    }

        /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Business
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * @return string
     */
    public function getClientIpAddress()
    {
        return $this->client_ip_address;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return \MongoId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIssuerId()
    {
        return $this->issuer_id;
    }

    /**
     * @return string
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return \MongoId
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return string
     */
    public function getEventId()
    {
        return $this->event_id;
    }

    /**
     * @param string $event_id
     */
    public function setEventId($event_id)
    {
        $this->event_id = $event_id;
    }

    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->request_id;
    }

    /**
     * @param string $request_id
     */
    public function setRequestId($request_id)
    {
        $this->request_id = $request_id;
    }

}
