<?php
namespace StreetVC\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use StreetVC\TransactionBundle\Document\BankAccount;

class BankAccountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array('label'=>'Bank Name'));
        $builder->add('account_holder', 'text', [ 'label' => 'Name of Account Holder' ]);
        $builder->add('type', 'choice', array('choices' => BankAccount::getTypes()));
        $builder->add('routing_number', null, array('data'=>'075996016', 'read_only'=>true, 'attr'=>array('placeholder'=>'075996016')));
        $builder->add('account_number', null, array('data'=>'123456789', 'read_only'=>true,'attr'=>array('placeholder'=>'123456789')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'StreetVC\TransactionBundle\Document\BankAccount'));
    }

    public function getName()
    {
        return "bank_account";
    }
}
