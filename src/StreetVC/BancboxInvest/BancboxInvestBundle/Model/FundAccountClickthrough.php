<?php
namespace StreetVC\BancboxInvest\BancboxInvestBundle\Model;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

use GuzzleHttp\ToArrayInterface;

class FundAccountClickthrough implements ToArrayInterface
{
    use \GuzzleHttp\HasDataTrait;
    use \Sd\BaseBundle\Has\IdentifiableTrait;

    /**
     * #MongoDB\Hash
     * @var unknown
     */
//    protected $data = [];

    public $required = [];

    public function __construct($data)
    {
        $this->id = new \MongoId();
        $this->data = $data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

}
