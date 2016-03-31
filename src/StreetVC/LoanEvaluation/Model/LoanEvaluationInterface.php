<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/4/14
 * Time: 7:32 PM
 */

namespace StreetVC\LoanEvaluation\Model;


interface LoanEvaluationInterface {

    public function getRate();

    public function getTerm();

    public function getAmount();

    public function getGrade();

    public function getLoanRequest();
}
