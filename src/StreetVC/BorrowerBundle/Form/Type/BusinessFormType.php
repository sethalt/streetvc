<?php
namespace StreetVC\BorrowerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use StreetVC\BorrowerBundle\Document\Business;
use Sd\BaseBundle\Document\Address;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * @DI\FormType
 */
class BusinessFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('legal_name', 'text', array ('label'=>'biz.legal.name', 'translation_domain' => 'business'));
        $builder->add('dbas', 'text', array ('label'=>'biz.legal.dbas', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('phone', 'text', array( 'label'=>'biz.legal.phone', 'translation_domain' => 'business' ));
        $builder->add('legal_structure', 'choice', array( 'choices'=>Business::getLegalStructures(), 'label'=>'biz.legal.structure', 'translation_domain' => 'business' ));
        $builder->add('tax_id', 'text', array('data'=>'111111111', 'read_only'=>true, 'label'=>'biz.legal.taxid', 'translation_domain' => 'business'));
        $builder->add('website', 'url', array( 'label'=>'biz.website', 'translation_domain' => 'business', 'required'=>false, 'attr'=>array('placeholder'=>'http://www.streetvc.com' )));
        $builder->add('industry', 'document', array(
                'label'=>'biz.legal.industry',
                'translation_domain' => 'business',
                'class' => 'StreetVC\BorrowerBundle\Document\Industry',
                'query_builder' => function (DocumentRepository $repository) {
                    return $repository->createQueryBuilder();
                    //                        ->field('id')->equals(new \MongoRegex("/^\\d\\d\$/"));
                }
        ));
        $builder->add('year_established', 'choice', array ('choices'=>$this->buildYearChoices(), 'label'=>'biz.legal.year_established', 'translation_domain' => 'business'));
        $builder->add('registration_state', 'choice', array('choices'=>Address::getStateAbbreviations(), 'label' => 'biz.state_of_incorporation', 'translation_domain' => 'business'));
        $builder->add('address', 'sd_base_address', array( 'label'=>'biz.legal.address', 'translation_domain' => 'business'));
        $builder->add('bricks_and_mortar', 'choice', [ 'choices'=>array(1=>'yes'), 'expanded'=>true, 'empty_value'=>'no', 'label'=>'biz.bricks_mortar', 'translation_domain' => 'business', 'required' => false ]);
        $builder->add('number_locations', 'number', [ 'label'=>'biz.number_locations', 'translation_domain' => 'business', 'required' => false ]);
        $builder->add('time_at_current_location', 'text', [ 'label'=>'biz.time_at_current_location', 'translation_domain' => 'business', 'required' => false ]);
        $builder->add('bio', 'textarea', array('label'=>'biz.description', 'translation_domain' => 'business'));
        $builder->add('video', 'text', array( 'label'=>'biz.video', 'translation_domain' => 'business', 'required'=>false ));
        
        $builder->add('annual_revenue', 'integer', array( 'label' => 'biz.legal.annual_revenue', 'translation_domain' => 'business', 'required'=>false ));
        $builder->add('net_profit', 'integer', array( 'label' => 'biz.legal.net_profit', 'translation_domain' => 'business', 'required'=>false ));
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
        $builder->add('cf_amount_due', 'integer', array('label'=>'biz.cf_amount_due', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('cf_largest_amount_outstanding', 'integer', array('label'=>'biz.cf_largest_amount_outstanding', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('cf_final_payment_date', 'date', array('years'=>range(2014, 2040), 'label'=>'biz.cf_final_payment_date', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('assets_real_estate_value', 'integer', array('label'=>'biz.assets_real_estate_value', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('assets_real_estate_equity', 'integer', array('label'=>'biz.assets_real_estate_equity', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('assets_equipment', 'integer', array('label'=>'biz.assets_equipment', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('assets_inventory', 'integer', array('label'=>'biz.assets_inventory', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('assets_accounts_receivable', 'integer', array('label'=>'biz.assets_accounts_receivable', 'translation_domain' => 'business', 'required'=>false));
        $builder->add('assets_cash', 'integer', array('label'=>'biz.assets_cash', 'translation_domain' => 'business', 'required'=>false));
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
        return 'business';
    }
}
