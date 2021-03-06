<?php
namespace StreetVC\ActivityBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Spy\TimelineBundle\Document\ActionComponent as BaseActionComponent;

/**
 * @MongoDB\Document
 */
class ActionComponent extends BaseActionComponent
{
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    protected $id;
}
