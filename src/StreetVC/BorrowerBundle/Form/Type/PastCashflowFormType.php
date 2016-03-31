<?php
namespace StreetVC\BorrowerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use StreetVC\BorrowerBundle\Document\MonthlyFinancial;

class PastCashflowFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('month', 'choice', array(
                    'choices'=>MonthlyFinancial::pastThreeMonths()
            ))
            ->add('cashflow', 'integer', array('attr'=>array('class'=>'money')))
        ;
    }

    public function getName()
    {
        return 'past_cashflow';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'StreetVC\BorrowerBundle\Document\MonthlyFinancial',
            'empty_data' => new MonthlyFinancial(),
        ));
    }
}