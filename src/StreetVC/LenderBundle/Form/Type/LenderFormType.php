<?php
namespace StreetVC\LenderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LenderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name')
            ->add('last_name')
            ->add('phone_number', 'text')
            ->add('date_of_birth', 'birthday', array('label' => 'Date of Birth'))
            ->add('address', 'sd_base_address')
            ->add('social_security_number', 'text', array('error_bubbling'=>true, 'data'=>'222-22-2222', 'read_only'=>true, 'attr'=>array('placeholder'=>'xxx-xx-xxxx')))
            ->add('submit', 'submit', array('label' => 'Create Investor Profile'));
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $options = [ 'data_class' => 'StreetVC\UserBundle\Document\User' ];
        $resolver->setDefaults($options);

    }

    public function getName()
    {
        return 'lender';
    }
}
