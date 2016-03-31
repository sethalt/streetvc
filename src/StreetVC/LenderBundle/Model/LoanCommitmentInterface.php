<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/6/14
 * Time: 5:57 AM
 */

namespace StreetVC\LenderBundle\Model;


interface LoanCommitmentInterface {

    public function getAmount();
    public function isValid();
    public function getEscrow();
    public function getId();
}
