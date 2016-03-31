<?php
namespace StreetVC\BorrowerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use StreetVC\BorrowerBundle\Document\Business;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\FormType
 */
class BusinessFinancialFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('annual_revenue', 'integer', array( 'label' => 'biz.legal.annual_revenue', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('class'=>'money') ));
        $builder->add('net_profit', 'integer', array( 'label' => 'biz.legal.net_profit', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('class'=>'money') ));
        $builder->add('has_cashflow', 'choice', array('choices'=>array(1=>'yes'), 'expanded'=>true, 'data'=>1, 'empty_value'=>'no', 'label' => 'biz.legal.have_cashflow', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('tooltip'=>'biz.cashflow_tip')));
        $builder->add('past_cashflow', 'collection', array(
                'label' => 'biz.financial.past_cashflow', 'translation_domain' => 'business',
                'type' => new PastCashflowFormType(),
        ));
        $builder->add('cyclical_business', 'choice', array('choices'=>array(1=>'yes'), 'expanded'=>true, 'empty_value'=>'no',  'label' => 'biz.legal.cyclical_business', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('tooltip'=>'biz.cashflow_tip')));
        $builder->add('past_revenue', 'collection', array(
                'label' => 'biz.financial.past_revenue', 'translation_domain' => 'business',
                'type' => new PastRevenueFormType(),
        ));
        $builder->add('credit_facilities', 'choice', array('choices'=>array(true=>'yes'), 'expanded'=>true, 'empty_value'=>'no', 'label'=>'biz.credit_facilities', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('cf_amount_due', 'integer', array('label'=>'biz.cf_amount_due', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('class'=>'money')));
        $builder->add('cf_largest_amount_outstanding', 'integer', array('label'=>'biz.cf_largest_amount_outstanding', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('class'=>'money')));
        $builder->add('cf_final_payment_date', 'date', array('years'=>range(2014, 2040), 'label'=>'biz.cf_final_payment_date', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('assets_real_estate_value', 'integer', array('label'=>'biz.assets_real_estate_value', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('class'=>'money')));
        $builder->add('assets_real_estate_equity', 'integer', array('label'=>'biz.assets_real_estate_equity', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('class'=>'money')));
        $builder->add('assets_equipment', 'integer', array('label'=>'biz.assets_equipment', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('class'=>'money')));
        $builder->add('assets_inventory', 'integer', array('label'=>'biz.assets_inventory', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('class'=>'money')));
        $builder->add('assets_accounts_receivable', 'integer', array('label'=>'biz.assets_accounts_receivable', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('class'=>'money')));
        $builder->add('assets_cash', 'integer', array('label'=>'biz.assets_cash', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('class'=>'money')));
        $builder->add('number_employees', 'integer', array('label'=>'biz.number_employees', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('own_lease_location', 'choice', array('choices'=>array(true=>'yes'), 'expanded'=>true, 'empty_value'=>'no', 'label'=>'biz.own_lease_location', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('remaining_term_of_lease', 'integer', array('label'=>'biz.remaining_term_of_lease', 'translation_domain' => 'business', 'required'=>false));

    }

    public function buildYearChoices() {
        $distance = 150;
        $yearsBefore = date('Y', mktime(0, 0, 0, date("m"), date("d"), date("Y") - $distance));
        $today = date('Y', mktime(0, 0, 0, date("m"), date("d"), date("Y")));
        return array_combine(range($today, $yearsBefore), range($today, $yearsBefore));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults( [
            'data_class'=>'StreetVC\BorrowerBundle\Document\Business',
            'allow_extra_fields' => true,
        ]);
    }

    public function getName()
    {
        return 'business_financial';
    }
}
