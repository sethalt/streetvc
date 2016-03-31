<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 12/2/2014
 * Time: 12:22 PM
 */

namespace StreetVC\BorrowerBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Repayment
 * @package StreetVC\BorrowerBundle\Document
 * @MongoDB\EmbeddedDocument
 */
class MonthlyFinancial {

    /**
     * @var \MongoId
     * @MongoDB\Id(strategy="Auto")
     */
    protected $id;
    
    /**
     * @var float
     * @MongoDB\Float
     * @JMS\Expose()
     */
    protected $revenue;
    
    /**
     * @var float
     * @MongoDB\Float
     * @JMS\Expose()
     */
    protected $expenses;
    
    /**
     * @var float
     * @MongoDB\Float
     * @JMS\Expose()
     */
    protected $cashflow;
    
    /**
     * @var string
     * @MongoDB\String
     * @JMS\Expose()
     */
    protected $month;
    
    public function setRevenue($revenue)
    {
        $this->revenue = $revenue;
    }

    public function getRevenue()
    {
        return $this->revenue;
    }

    public function setExpenses($expenses)
    {
        $this->expenses = $expenses;
    }

    public function getExpenses()
    {
        return $this->expenses;
    }

    public function setCashflow($cashflow)
    {
        $this->cashflow = $cashflow;
    }

    public function getCashflow()
    {
        return $this->cashflow;
    }

    public function setMonth($month)
    {
        $this->month = $month;
    }

    public function getMonth()
    {
        return $this->month;
    }
    
    static function monthAbbreviations(){
        return array('jan'=>'jan', 'feb'=>'feb', 'mar'=>'mar', 'apr'=>'apr', 'may'=>'may', 'jun'=>'jun', 'jul'=>'jul', 'aug'=>'aug', 'sep'=>'sep', 'oct'=>'oct','nov'=>'nov', 'dec'=>'dec');
    }
    static function pastThreeMonths(){
        return array('previousMonth'=>'Previous month', '2months'=>'2 months ago', '3months'=>'3 months ago');
    }

}
