<?php
namespace StreetVC\TransactionBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @MongoDB\Document
 * #MongoDB\InheritanceType("SINGLE_COLLECTION")
 * #MongoDB\DiscriminatorField("type")
 * #MongoDB\DiscriminatorMap({"transaction"="Transaction","withdrawal"="Withdrawal", "deposit"="Deposit"})
 */
class Transaction
{
    /**
     * @MongoDB\Id(strategy="auto")
     * @var string
     */
    protected $id;

    /**
     * @MongoDB\Float
     * @var float
     */
    protected $amount;

    /**
     *
     * @var unknown
     */
    protected $from;

    protected $to;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\UserBundle\Document\User", simple=true)
     * @var \StreetVC\UserBundle\Document\User
     */
    protected $user;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $description;

    /**
     * @MongoDB\Date
     * @var \DateTime
     */
    protected $created;

    /**
     * @MongoDB\Date
     * @var \DateTime
     */
    protected $updated;


    public function __construct()
    {
        $this->created = new \DateTime();
    }

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get amount
     *
     * @return float $amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set user
     *
     * @param StreetVC\UserBundle\Document\User $user
     * @return self
     */
    public function setUser(\StreetVC\UserBundle\Document\User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return StreetVC\UserBundle\Document\User $user
     */
    public function getUser()
    {
        return $this->user;
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

    /**
     * Set created
     *
     * @param date $created
     * @return self
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return date $created
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param date $updated
     * @return self
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * Get updated
     *
     * @return date $updated
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
