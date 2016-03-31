<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/4/14
 * Time: 11:20 PM
 */

namespace StreetVC\LoanEvaluation\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use StreetVC\LoanBundle\Document\LoanRequest;

/**
 * Class RandomLoanEvaluation
 * @package StreetVC\LoanEvaluation\Model
 */
class RandomLoanEvaluation extends LoanEvaluation implements LoanEvaluationInterface
{

    public function __construct(LoanRequest $loanRequest)
    {
        parent::__construct($loanRequest);
        $range = range(5,25);
        $this->rate = $range[array_rand($range)]*.01;
        $this->amount = rand(10000,250000);
        $grades = range('A','G');
        $this->grade = $grades[array_rand($grades)];
        $this->term = rand(3,36);
    }


}

