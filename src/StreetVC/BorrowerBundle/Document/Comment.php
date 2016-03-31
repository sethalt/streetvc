<?php
namespace StreetVC\BorrowerBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\CommentBundle\Document\Comment as BaseComment;
use FOS\CommentBundle\Model\SignedCommentInterface;

/**
 * @MongoDB\Document
 * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Comment extends BaseComment implements SignedCommentInterface
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * Thread of this comment
     *
     * @var Thread
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\BorrowerBundle\Document\Thread")
     */
    protected $thread;

    /** @MongoDB\ReferenceOne(targetDocument="StreetVC\UserBundle\Document\User", simple=true) */
    protected $author;

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor(\Symfony\Component\Security\Core\User\UserInterface $author)
    {
        $this->author = $author;
        return $this;
    }

    public function getAuthorName()
    {
        if (null === $this->getAuthor()) {
            return 'Anonymous';
        }

        return $this->getAuthor()->getUsername();
    }
}