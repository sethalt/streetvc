<?php

namespace Sd\BaseBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/** @MongoDB\EmbeddedDocument */

class Address {

    /** @MongoDB\String */
    protected $name;

    /** @MongoDB\String */
    protected $address;

    /** @MongoDB\String */
    protected $address2;

    /** @MongoDB\String */
    protected $city;

    /** @MongoDB\String */
    protected $state;

    /** @MongoDB\String */
    protected $zip;

    public function __toString()
    {
        return $this->format();
    }

    public function format()
    {
        $address = $this->getAddress() . " " . $this->getAddress2() . " " . $this->getCity() . ", " . $this->getState() . " " . $this->getZip();
        return $address;
    }

    public function isComplete()
    {
        return $this->address && $this->city && $this->zip;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress2($address2) {
        $this->address2 = $address2;
    }

    public function getAddress2()
    {
        return $this->address2;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function getState()
    {
        return 'WI'; // $this->state;
    }

    public function setZip($zip) {
        $this->zip = $zip;
    }

    public function getZip()
    {
        return $this->zip;
    }
    public static function getStateAbbreviations()
    {
        $states = array(
                'AL'=>'ALABAMA',
                'AK'=>'ALASKA',
                'AZ'=>'ARIZONA',
                'AR'=>'ARKANSAS',
                'CA'=>'CALIFORNIA',
                'CO'=>'COLORADO',
                'CT'=>'CONNECTICUT',
                'DE'=>'DELAWARE',
                'DC'=>'DISTRICT OF COLUMBIA',
                'FL'=>'FLORIDA',
                'GA'=>'GEORGIA',
                'HI'=>'HAWAII',
                'ID'=>'IDAHO',
                'IL'=>'ILLINOIS',
                'IN'=>'INDIANA',
                'IA'=>'IOWA',
                'KS'=>'KANSAS',
                'KY'=>'KENTUCKY',
                'LA'=>'LOUISIANA',
                'ME'=>'MAINE',
                'MD'=>'MARYLAND',
                'MA'=>'MASSACHUSETTS',
                'MI'=>'MICHIGAN',
                'MN'=>'MINNESOTA',
                'MS'=>'MISSISSIPPI',
                'MO'=>'MISSOURI',
                'MT'=>'MONTANA',
                'NE'=>'NEBRASKA',
                'NV'=>'NEVADA',
                'NH'=>'NEW HAMPSHIRE',
                'NJ'=>'NEW JERSEY',
                'NM'=>'NEW MEXICO',
                'NY'=>'NEW YORK',
                'NC'=>'NORTH CAROLINA',
                'ND'=>'NORTH DAKOTA',
                'OH'=>'OHIO',
                'OK'=>'OKLAHOMA',
                'OR'=>'OREGON',
                'PW'=>'PALAU',
                'PA'=>'PENNSYLVANIA',
                'RI'=>'RHODE ISLAND',
                'SC'=>'SOUTH CAROLINA',
                'SD'=>'SOUTH DAKOTA',
                'TN'=>'TENNESSEE',
                'TX'=>'TEXAS',
                'UT'=>'UTAH',
                'VT'=>'VERMONT',
                'VI'=>'VIRGIN ISLANDS',
                'VA'=>'VIRGINIA',
                'WA'=>'WASHINGTON',
                'WV'=>'WEST VIRGINIA',
                'WI'=>'WISCONSIN',
                'WY'=>'WYOMING',
        );
    
        return $states;
    }

}