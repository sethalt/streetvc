<?php
namespace StreetVC\BorrowerBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

class Payment
{
    /**
     * @MongoDB\Id(strategy="auto")
     * @JMS\Expose
     * @var unknown
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LoanBundle\Document\LoanRequest", simple=true)
     * @JMS\Expose
     * @var LoanRequest
     */
    protected $loanRequest;

    /**
     * #MongoDB\ReferenceOne(targetDocument="StreetVC\BorrowerBundle\Document\Loan", simple=true)
     * @var Loan
     */
    protected $loan;

    /**
     * @MongoDB\Float
     * @JMS\Expose
     * @var float
     */
    protected $amount;

    /**
     * @MongoDB\Date
     * @JMS\Expose
     * @var \DateTime
     */
    protected $date;

}