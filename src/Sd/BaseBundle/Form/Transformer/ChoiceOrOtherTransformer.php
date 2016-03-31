<?php

namespace Sd\BaseBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class ChoiceOrOtherTransformer implements DataTransformerInterface
{
    protected $choices;

    public function __construct($choices)
    {
        $this->choices = $choices;
    }
    /**
     * Transform database value into presentation value (choice or other text)
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\DataTransformerInterface::transform()
     */
    public function transform($value)
    {
        return in_array($value, $this->choices) ? array('choice'=>$value) : array('other'=>$value);
    }

    public function reverseTransform($value)
    {
        return isset($value['other']) ? $value['other'] : $value['choice'];
    }

}