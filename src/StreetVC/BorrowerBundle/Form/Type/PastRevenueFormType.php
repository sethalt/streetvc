<?php
namespace StreetVC\BorrowerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use StreetVC\BorrowerBundle\Document\MonthlyFinancial;

class PastRevenueFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('month', 'choice', array(
                    'choices'=>MonthlyFinancial::monthAbbreviations()
            ))
            ->add('revenue', 'integer', array('attr'=>array('class'=>'money')))
            ->add('cashflow', 'integer', array('attr'=>array('class'=>'money')))
        ;
    }

    public function getName()
    {
        return 'past_revenue';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'StreetVC\BorrowerBundle\Document\MonthlyFinancial',
            'empty_data' => new MonthlyFinancial(),
        ));
    }
}