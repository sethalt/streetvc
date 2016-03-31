<?php
namespace StreetVC\UserBundle\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MinAgeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $minAge = strtotime(sprintf("-%s YEAR", $constraint->age));
        if(strtotime($value->format("Y-m-d")) > $minAge)
        {
            $this->setMessage($constraint->message,
                array('{{ age }}' => $constraint->age));
            return false;
        }
        return true;
    }
}