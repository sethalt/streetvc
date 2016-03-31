<?php
namespace StreetVC\TransactionBundle\Traits;

use JMS\Serializer\Annotation\Expose;

trait FiniteStateTrait
{
    /**
     * @MongoDB\String
     * @Expose()
     * @var string
     */
    private $state;

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getFiniteState()
    {
        return $this->state;
    }

    public function setFiniteState($state)
    {
        $this->state = $state;
    }
}
