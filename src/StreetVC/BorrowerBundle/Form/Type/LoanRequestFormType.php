<?php
namespace StreetVC\BorrowerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\FormType
 * @author dao
 *
 */
class LoanRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('label'=>'lr.title', 'translation_domain' => 'loan_request'));
        $builder->add('funding_goal', 'integer', array('label'=>'lr.funding_goal', 'translation_domain' => 'loan_request', 'attr'=>array('class'=>'money')));
        $uses = [ 
          'cash flow' => 'cash flow', 
          'day-to-day operating expenses'=>'day-to-day operating expenses', 
          'inventory'=>'inventory', 
          'additional plant/equipment/vehicles'=>'additional plant/equipment/vehicles',
          'reserve/cushion'=>'reserve/cushion', 
          'real estate'=>'real estate', 
          'replace old plan/equipment/vehicles'=>'replace old plan/equipment/vehicles', 
          'start business'=>'start business', 
          'repayment or debt'=>'repayment or debt', 
          'payroll'=>'payroll', 
          'construction for new / expanded facility'=>'construction for new / expanded facility' 
        ];
        $builder->add('use_of_loan_proceeds', 'choice', array('choices'=>$uses, 'label'=>'lr.use_of_loan_proceeds', 'translation_domain' => 'loan_request'));
        $durations = [ '3' => 3, '6' => 6, '12' => 12, '24' => 24, '36'=> 36 ];
        $builder->add('term', 'choice', array('choices'=>$durations, 'label'=>'lr.term', 'translation_domain' => 'loan_request'));
        $builder->add('description', 'textarea', array('label'=>'lr.description', 'translation_domain' => 'loan_request'));
        $builder->add('video', 'text', array( 'label'=>'lr.video', 'translation_domain' => 'loan_request', 'required'=>false ));

//        $builder->add('minimum_funding_amount');
//        $builder->add('maximum_funding_amount');
        /*
        $builder->add('start_date', 'date', array('years'=>range(date('Y'), date('Y')+5)));
        $builder->add('close_date', 'date', array('years'=>range(date('Y'), date('Y')+5)));
        */
        /*
        $builder->add('additional_revenue');
        $builder->add('time_to_utilize');
        */
    //    $builder->add('save', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults( array(
            'data_class'=>'StreetVC\LoanBundle\Document\LoanRequest',
            'allow_extra_fields' => true
        ));
    }

    public function getName()
    {
        return 'loan_request';
    }
}
