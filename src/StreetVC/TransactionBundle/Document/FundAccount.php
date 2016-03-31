<?php

namespace StreetVC\TransactionBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @MongoDB\Document
 * @JMS\ExclusionPolicy("all")
 */
class FundAccount
{
    use \Sd\BaseBundle\Has\CreatedAndUpdatedTrait;
    use \Sd\BaseBundle\Has\IdentifiableTrait;

    /** @MongoDB\Float */
    private $amount;

    /** @MongoDB\String */
    private $client_ip_address;

    /** @MongoDB\String */
    private $submit_timestamp;

    /** @MongoDB\String */
    private $investor_id;

    /** @MongoDB\String */
    private $issuer_id;

    /** @MongoDB\String */
    private $linked_bank_account_id;

    /** @MongoDB\String */
    private $reference_id;

    /** @MongoDB\String */
    private $memo;

    /** @MongoDB\String */
    private $text;

    public function __construct()
    {
        $this->id = (string) new \MongoId();
    }
}