<?php
namespace Sd\BaseBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/** @MongoDB\EmbeddedDocument */
class PhoneNumber
{
    /** @MongoDB\String */
    protected $number;

    /** @MongoDB\String */
    protected $type;

    public function __toString()
    {
        return $this->number . ' (' . $this->type . ')';
    }

    public static function getTypes()
    {
        return array('mobile'=>'mobile', 'home'=>'home', 'work'=>'work', 'pager'=>'pager');
    }

    /**
     * Set number
     *
     * @param string $number
     * @return \PhoneNumber
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return string $number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return \PhoneNumber
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }
}
