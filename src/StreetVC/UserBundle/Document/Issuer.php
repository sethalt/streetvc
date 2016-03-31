<?php
namespace StreetVC\User\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use JMS\Serializer\Annotation\Expose;
use Doctrine\Common\Collections\ArrayCollection;
use StreetVC\UserBundle\Document\User;
use StreetVC\TransactionBundle\Document\BankAccount;

/**
 * @MongoDB\EmbeddedDocument
 * @JMS\ExclusionPolicy("all")
 */
class Issuer
{
    use \StreetVC\BancboxInvest\BancboxInvestBundle\Traits\BancboxAccountTrait;
    use \Sd\BaseBundle\Has\CreatedAndUpdatedTrait;
    use \Sd\BaseBundle\Has\IdentifiableTrait;

    public function __construct()
    {
        $this->id = (string) new \MongoId();
//        $this->loans = new ArrayCollection();
//        $this->internal_account = new BankAccount();
        $this->linked_accounts = new ArrayCollection();
        $this->created = new \DateTime();
    }

}