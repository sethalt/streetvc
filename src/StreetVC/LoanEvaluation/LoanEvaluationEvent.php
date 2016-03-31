<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 10/20/2014
 * Time: 2:44 PM
 */

namespace StreetVC\LoanEvaluation;


use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\LoanEvaluation\Model\LoanEvaluationInterface;
use Symfony\Component\EventDispatcher\Event;

class LoanEvaluationEvent extends Event {

    const
        EVALUATE = 'loanrequest.evaluate',
        EVALUATED = 'loanrequest.evaluated'
    ;

    /**
     * @var LoanRequest
     */
    protected $loanRequest;

    /**
     * @var LoanEvaluationInterface
     */
    protected $evaluation;

    public function __construct(LoanRequest $loanRequest)
    {
        $this->loanRequest = $loanRequest;
    }

    /**
     * @return LoanRequest
     */
    public function getLoanRequest()
    {
        return $this->loanRequest;
    }

    /**
     * @return LoanEvaluationInterface
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }

    public function setEvaluation(LoanEvaluationInterface $evaluation)
    {
        $this->evaluation = $evaluation;
    }

}
