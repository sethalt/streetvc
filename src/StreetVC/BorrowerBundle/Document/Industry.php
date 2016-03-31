<?php
namespace StreetVC\BorrowerBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document()
 */
class Industry
{
    /**
     * @MongoDB\Id()
     * @var \MongoId
     */
    protected $id;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $title;

    /**
     * @var int
     * @MongoDB\Int
     * industry code
     */
    protected $code;

    /**
     * @var float
     * @MongoDB\Float
     */
    protected $adjustment;

    public function __toString()
    {
        return $this->title;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getAdjustment()
    {
        return $this->adjustment;
    }
}
