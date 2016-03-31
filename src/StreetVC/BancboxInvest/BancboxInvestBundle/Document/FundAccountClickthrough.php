<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/8/14
 * Time: 5:44 AM
 */

namespace StreetVC\BancboxInvest\BancboxInvestBundle\Document;

use StreetVC\BancboxInvest\BancboxInvestBundle\Model\FundAccountClickthrough as Base;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class FundAccountClickthrough
 * @package StreetVC\BancboxInvest\BancboxInvestBundle\Document
 * @MongoDB\Document
 */
class FundAccountClickthrough extends Base
{

    /**
     * #MongoDB\ReferenceOne(targetDocument="StreetVC\LenderBundle\Document\Lender", simple=true)
     * @var Lender;
     */
    protected $lender;

    /**
     * @MongoDB\Id
     * @var \MongoId
     */
    protected $id;

    /**
     * @MongoDB\Hash
     * @var array
     */
    protected $data;

}
