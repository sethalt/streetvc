<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/4/14
 * Time: 7:34 PM
 */

namespace StreetVC\LoanEvaluation\Model;

abstract class LoanEvaluation implements LoanEvaluationInterface {

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

    public function __construct(LoanRequestInterface $loanRequest)
    {
        $this->loan_request = $loanRequest;
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
