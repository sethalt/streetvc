<?php

namespace StreetVC\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class RegistrationFormType
 * @package StreetVC\UserBundle\Form\Type
 * #DI\FormType
 */
class RegistrationFormType extends BaseRegistrationFormType
{
    
    private $class;
    
    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', 'text', array('label'=>'reg.first_name', 'translation_domain' => 'registration'))
            ->add('last_name', 'text', array('label'=>'reg.last_name', 'translation_domain' => 'registration'))
        
            ->add('email', 'email', array('label' => 'reg.email', 'translation_domain' => 'registration'))
     //       ->add('username', null, array('label' => 'reg.username', 'translation_domain' => 'registration', 'error_bubbling'=>true))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'error_bubbling'=>true,
                'options' => array('translation_domain' => 'registration'),
                'first_options' => array('label' => 'reg.password'),
                'second_options' => array('label' => 'reg.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
              ))
        ;
//        parent::buildForm($builder, $options);
//        $builder->remove('username');
//        $builder->add('social_security_number');
//        $builder->add('phone_number', 'text');
//        $builder->add('date_of_birth', 'birthday');
//        $builder->add('bank_account', 'bank_account');
//        $builder->add('address', 'sd_base_address');
        $builder
            ->add('phone_number', 'text')
     //       ->add('date_of_birth', 'birthday', [ 'label' => 'reg.birthdate', 'translation_domain' => 'registration', 'data'=> new \DateTime('01-01-1970'), 'years'=>range(1900, date('Y')), 'attr' => ['class'=>'date']])
     //       ->add('social_security_number', 'text', ['error_bubbling'=>true, 'data'=>'222-22-2222', 'read_only'=>true, 'label' => 'reg.ssn', 'translation_domain' => 'registration',  'attr'=> ['placeholder'=>'xxx-xx-xxxx'] ])
        ;
     //   $builder->add('allow_background_check', 'checkbox', [ 'data'=>true, 'label'=>'reg.background_check', 'translation_domain' => 'registration',  'mapped'=>false, 'attr'=>['class'=>'checkbox'] ]);
     //   $builder->add('certify_true', 'checkbox', ['data'=>true, 'label'=>'reg.certify_true', 'translation_domain' => 'registration',  'mapped'=>false, 'attr'=>['class'=>'checkbox'] ]);
     //   $builder->add('cashflow_positive', 'checkbox', ['data'=>true, 'label'=>'reg.cashflow_positive', 'required'=>false, 'translation_domain' => 'registration', 'attr'=>['class'=>'checkbox'] ]);
    }

    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => $this->class,
                'intention'  => 'registration',
                'error_mapping' => array(
                        'email' => 'social_security_number'
                ),
        ));
    }
    

    public function getName()
    {
        return 'streetvc_user_registration';
    }
}
