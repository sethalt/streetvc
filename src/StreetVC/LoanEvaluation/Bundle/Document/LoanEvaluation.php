<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/8/14
 * Time: 3:06 AM
 */

namespace StreetVC\LoanEvaluation\Bundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Sd\BaseBundle\Has\IdentifiableTrait;
use StreetVC\LoanEvaluation\Model\LoanEvaluation as BaseEvaluation;
use StreetVC\LoanEvaluation\Model\LoanRequestInterface;
use JMS\Serializer\Annotation as JMS;

/**
 * Class LoanEvaluation
 * @package StreetVC\LoanEvaluation\Bundle\Document
 * @MongoDB\Document
 */
class LoanEvaluation extends BaseEvaluation {

    use IdentifiableTrait;

    public function __construct(LoanRequestInterface $loanRequest)
    {
        $this->loan_request = $loanRequest;
    }

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LoanBundle\Document\LoanRequest", simple=true)
     * @var LoanRequestInterface
     */
    protected $loan_request;

    /**
     * @MongoDB\float
     * @var float
     */
    protected $rate;

    /**
     * @MongoDB\Int
     * @var integer
     */
    protected $term;

    /**
     * @MongoDB\Int
     * @var float
     */
    protected $amount;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $grade;

    /**
     * @var \DateTime
     * @MongoDB\Date
     * date user accepted this loan evaluation
     */
    protected $accepted_on;

    public function accept()
    {
        $this->accepted_on = new \DateTime();
    }

    public function getAcceptedOn()
    {
        return $this->accepted_on;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function getTerm()
    {
        return $this->term;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getGrade()
    {
        return $this->grade;
    }

    public function getLoanRequest()
    {
        return $this->loan_request;
    }

    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    public function setTerm($term)
    {
        $this->term = $term;
    }

    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
}
