<?php
namespace StreetVC\BorrowerBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\CommentBundle\Document\Thread as BaseThread;

/**
 * @MongoDB\Document
 * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Thread extends BaseThread
{

}