<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/4/14
 * Time: 7:29 PM
 */

namespace StreetVC\LoanEvaluation\Service;


use StreetVC\LoanEvaluation\Model\LoanRequestInterface;

interface LoanEvaluatorInterface {

    public static function evaluate(LoanRequestInterface $loanRequest);
}
