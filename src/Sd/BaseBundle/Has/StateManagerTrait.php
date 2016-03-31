<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/5/14
 * Time: 12:56 PM
 */

namespace Sd\BaseBundle\Has;

use Finite\Factory\FactoryInterface;
use Finite\StateMachine\StateMachineInterface;

trait StateManagerTrait {

    /**
     * @param $object
     * @return StateMachineInterface
     */
    public function getStateMachine($object)
    {
        if (!$this->sm) {
            $this->sm = $this->finite_factory->get($object, $this->graph);
        }
        return $this->sm;
    }

    /**
     * @param $object
     * @param $transition
     */
    private function applyTransition($object, $transition)
    {
        $this->getStateMachine($object)->apply($transition);
    }

    /**
     * @param $object
     * @param $transition
     * @return bool
     */
    private function canTransition($object, $transition)
    {
        return $this->getStateMachine($object)->can($transition);
    }

}
