<?php
namespace Sd\BaseBundle\Twig;

class SdBaseExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('checkmark', array($this, 'checkmarkFilter', array('is_safe'=>array('html')))),
        );
    }

    public function checkmarkFilter($value)
    {
        $class = $value ? 'icon-check' : 'icon-minus';
        return "<i class='$class' />";
    }

    public function getName()
    {
        return 'sd_base_extension';
    }
}