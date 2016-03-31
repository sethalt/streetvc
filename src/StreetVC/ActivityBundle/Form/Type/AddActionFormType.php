<?php

namespace StreetVC\ActivityBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * AddActionFormType
 *
 * @uses AbstractType
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class AddActionFormType extends AbstractType
{
    private $users;

    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
        $components = array(
            'User:1' => '[User] Chuck Norris',
            'User:2' => '[User] Vic Mac Mkey',
            'User:3' => '[User] Walter White',
            'Car:alfaromeo-159'  => '[Car] Alfa romeo 159',
            'Car:bugatti-veyron'  => '[Car] Bugatti Veyron',
        );
        */
        $components = array(
            'User:535ec17a6803fa980ebdb9b3' => 'doggie1',
            'Business:535ec3296803fa080e82a490' => 'convivio'
        );

        $verbs = array(
            'like'   => 'like',
            'fund' => 'funded',
        );

        $builder
        ->add('subject', 'choice', array(
            'choices' => $components
        ))
        ->add('verb', 'choice', array(
            'choices' => $verbs
        ))
        ->add('complementObject', 'choice', array(
            'choices' => $components,
            'required' => false,
        ))
        ->add('complementText', 'text', array(
            'required' => false,
        ))
        ->add('save', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'StreetVC\ActivityBundle\Form\Model\Action'
        ));
    }

    public function getName()
    {
        return 'streetvc_activity_action_type';
    }

}