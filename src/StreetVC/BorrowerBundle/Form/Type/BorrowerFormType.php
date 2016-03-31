<?php
namespace StreetVC\BorrowerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use StreetVC\BorrowerBundle\Document\Business;

class BorrowerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('address', 'sd_base_address')
            ->add('legal_name', 'text', array ( 'label'=>'Official Name of Business'))
            ->add('legal_structure')
            ->add('tax_id', 'text', array( 'label'=>'Tax ID/EIN (9 digits)'))
            ->add('registration_state', 'text', ['label'=>'State of Incorporation'])

        ;
        /*
        $builder->add('phone', 'text', array( 'label'=>'Official Business Phone Number' ));
        $builder->add('address', 'sd_base_address', array( 'label'=>'Official Business Address'));
        $builder->add('legal_structure', 'choice', array( 'choices'=>Business::getLegalStructures() ));
        $builder->add('website', 'url', array( 'label'=>'Website address', 'required'=>false ));
        $builder->add('bio', 'textarea', array('label'=>'Brief Description Of Your Business'));
        $builder->add('annual_revenue', 'integer', array( 'label' => 'Annual Revenue' ));
        $builder->add('bricks_and_mortar', 'checkbox', array( 'label'=>'Is Your Business Primarily Bricks-and-Mortar?' ));
        $builder->add('margin', 'integer', array('label'=>'Average Margin per Sale'));
        $builder->add('industry', 'document', array(
            'class' => 'StreetVC\BorrowerBundle\Document\Industry',
            'query_builder' => function ($repository) {
                return $repository->createQueryBuilder()->field('id')->equals(new \MongoRegex("/^\\d\\d\$/"));
            }
        ));
        /*
        $builder->add('industry', 'document', array(
            'class' => 'StreetVC\BorrowerBundle\Document\Industry',
//            'property' => 'title',
            'query_builder' => function ($repository)
            {
//                return $repository->createQueryBuilder()->field('parent')->exists(true); //sort('title', 'ASC');
                return $repository->createQueryBuilder(); //->sort('id', 'ASC');
            },
//            'group_by'=>'parent.title'),
        ));
        */
//        $builder->add('venture_funding', 'integer', array('label'=>'Amount of Venture Funding Raised'));
//        $builder->add('angel_funding', 'integer', array('label'=>'Amount of Angel Funding Raised'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class'=>'StreetVC\BorrowerBundle\Document\Business'));
    }

    public function getName()
    {
        return 'borrower';
    }
}