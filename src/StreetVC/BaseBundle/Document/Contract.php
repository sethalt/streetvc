<?php
namespace StreetVC\BaseBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;

/**
 * @MongoDB\Document
 * @author dao
 */
class Contract extends Document
{

    /**
     * @MongoDB\Date()
     * @var \DateTime
     */
    protected $date_signed;

    public function __construct()
    {
        $this->id = (string) new \MongoId();
    }

    public function sign()
    {
        $this->date_signed = new \DateTime();
    }
}
