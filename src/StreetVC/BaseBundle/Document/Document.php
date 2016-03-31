<?php
namespace StreetVC\BaseBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;

/**
 * @MongoDB\Document
 * @author dao
 */
class Document
{
    use \Sd\BaseBundle\Has\GridFSTrait;

    public function __construct()
    {
        $this->id = (string) new \MongoId();
    }
}
