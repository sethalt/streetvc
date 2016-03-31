<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/4/14
 * Time: 7:35 PM
 */

namespace StreetVC\LoanEvaluation\Model;


interface LoanRequestInterface {

    public function getId();

    public function getTerm();

    public function getTitle();

    public function getUser();
}
