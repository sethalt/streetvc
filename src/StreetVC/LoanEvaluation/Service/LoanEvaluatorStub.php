<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/4/14
 * Time: 7:31 PM
 */

namespace StreetVC\LoanEvaluation\Service;

use StreetVC\LoanEvaluation\Bundle\Document\LoanEvaluation;
use StreetVC\LoanEvaluation\Model\LoanRequestInterface;

class LoanEvaluatorStub implements LoanEvaluatorInterface {

    public static function evaluate(LoanRequestInterface $loanRequest)
    {
        $evaluation = new LoanEvaluation($loanRequest);
        $range = range(5,25);
        $evaluation->setRate($range[array_rand($range)]*.01);
        $evaluation->setAmount(rand(10000,250000));
        $grades = range('A','G');
        $evaluation->setGrade($grades[array_rand($grades)]);
        $evaluation->setTerm(rand(3,36));
        return $evaluation;
    }
}
