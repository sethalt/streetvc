<?php
namespace StreetVC\ActivityBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Spy\TimelineBundle\Document\Action as BaseAction;

/**
 * @MongoDB\Document
 */
class Action extends BaseAction
{
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    protected $id;
}
