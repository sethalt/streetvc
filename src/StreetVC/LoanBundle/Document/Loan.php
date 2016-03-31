<?php
namespace StreetVC\LoanBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use StreetVC\Model\IssuerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 * @author dao
 *
 */
class Loan
{
    use \StreetVC\TransactionBundle\Traits\FiniteStateTrait;
    use \Sd\BaseBundle\Has\CreatedAndUpdatedTrait;
    use \Sd\BaseBundle\Has\IdentifiableTrait;
    use \StreetVC\LoanBundle\Model\Traits\TermLoanTrait;

    const STATE_INITIAL = 'new';

    /**
     * @var IssuerInterface
     */
    protected $issuer;

    /**
     * @MongoDB\Date
     * @var \DateTime
     */
    protected $disbursed_on;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LoanBundle\Document\Escrow", simple=true)
     * @var Escrow
     */
    protected $escrow;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LoanBundle\Document\Disbursement", simple=true)
     * @var Disbursement
     */
    protected $disbursement;

    public function __construct()
    {
        $this->id = new \MongoId();
        $this->setFiniteState(self::STATE_INITIAL);
    }

    public function getIssuer()
    {
        return $this->issuer;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function isDisbursed()
    {
        return null != $this->disbursed_on;
    }
    public function getDisbursedOn()
    {
        return $this->disbursed_on;
    }

    /**
     * @return Disbursement
     */
    public function getDisbursement()
    {
        return $this->disbursement;
    }

    /**
     * @return Escrow
     */
    public function getEscrow()
    {
        return $this->escrow;
    }

}
