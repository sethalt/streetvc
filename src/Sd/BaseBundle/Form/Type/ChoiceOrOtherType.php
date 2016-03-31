<?php

namespace Sd\BaseBundle\Form\Type;

use Sd\BaseBundle\Form\Transformer\ChoiceOrOtherTransformer;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ChoiceOrOtherType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $options['expanded'] ? array_merge($options['choices'], array(''=>'Other')) : $options['choices'];
        $builder->add('choice', 'choice', array(
                'choices' => $choices,
                'empty_value' => 'Other, please specify',
                'empty_data' => null,
                'required' => false,
                'label' => false,
                'widget_control_group' => true,
                'widget_controls'=>false,
                'expanded' => $options['expanded']
        ));
        $builder->add('other', 'text', array(
                'widget_control_group' => true,
                'attr' => array('placeholder'=> 'Other'),
                'label' => false,
                'widget_controls' => false,
                'required' => false,
        ));
        $builder->addModelTransformer(new ChoiceOrOtherTransformer($options['choices']), 1);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
                'compound' => true,
                'choices' => null,
                'multiple' => false,
                'expanded' => false,
        ));
        $resolver->setRequired(array('choices'));
    }

    public function getParent() {
        return 'form';
    }

    public function getName() {
        return 'choice_or_other';
    }

}