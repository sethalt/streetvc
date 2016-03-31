<?php
namespace Sd\BaseBundle\Has;

trait CreatedAndUpdatedTrait
{

    /**
     * @MongoDB\Date
     * @JMS\Expose
     * @var \DateTime
     */
    protected $created;

    /**
     * @MongoDB\Date
     * @JMS\Expose
     * @var \DateTime
     */
    protected $modified;

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated(\DateTime $datetime = null)
    {
        $this->created = $datetime;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function setModified()
    {
        $this->modified = new \DateTime();
    }
}
