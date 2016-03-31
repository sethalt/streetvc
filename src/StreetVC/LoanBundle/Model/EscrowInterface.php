<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/6/14
 * Time: 6:04 AM
 */

namespace StreetVC\LoanBundle\Model;


interface EscrowInterface {

    public function getId();

    public function getFundingGoal();

    public function getCommitments();

}
