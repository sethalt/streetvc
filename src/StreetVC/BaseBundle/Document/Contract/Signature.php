<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/22/2014
 * Time: 1:52 PM
 */

namespace StreetVC\BaseBundle\Document\Contract;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Signature
 * @package StreetVC\BaseBundle\Document\Contract
 * @MongoDB\EmbeddedDocument()
 */
class Signature {

    /**
     * @MongoDB\Id()
     * @var \MongoId
     */
    protected $id;

    protected $user_id;

    public function getDate()
    {
        return new \DateTime("@".$this->id->getTimestamp());
    }

}
