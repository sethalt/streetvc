<?php
namespace StreetVC\UserBundle\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MinAge extends Constraint
{
    public $message = 'The user must be {{ age }} or over';
    public $age = 18;

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}