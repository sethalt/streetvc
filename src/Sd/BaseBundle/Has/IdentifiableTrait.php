<?php
namespace Sd\BaseBundle\Has;

trait IdentifiableTrait
{
    /**
     * @MongoDB\Id(strategy="auto")
     * @JMS\Expose
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }

}