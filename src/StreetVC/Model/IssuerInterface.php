<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/18/14
 * Time: 5:35 PM
 */

namespace StreetVC\Model;


interface IssuerInterface {

    public function getTaxId();
    public function getLegalName();
    public function getFirstName();
    public function getLastName();
    public function getAddress();
//    public function getBusinessAddress();

}
