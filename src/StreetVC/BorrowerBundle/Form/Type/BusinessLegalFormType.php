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
class BusinessLegalFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('legal_name', 'text', array ('label'=>'biz.legal.name', 'translation_domain' => 'business'));
        $builder->add('tax_id', 'text', array('data'=>'111111111', 'read_only'=>true, 'label'=>'biz.legal.taxid', 'translation_domain' => 'business'));
        $builder->add('registration_state', 'choice', array('choices'=>Address::getStateAbbreviations(), 'label' => 'biz.state_of_incorporation', 'translation_domain' => 'business'));
        $builder->add('phone', 'text', array( 'label'=>'biz.legal.phone', 'translation_domain' => 'business' ));
        $builder->add('address', 'sd_base_address', array( 'label'=>'biz.legal.address', 'translation_domain' => 'business'));
        $builder->add('legal_structure', 'choice', array( 'choices'=>Business::getLegalStructures(), 'label'=>'biz.legal.structure', 'translation_domain' => 'business' ));
        $builder->add('industry', 'document', array(
                'label'=>'biz.legal.industry',
                'translation_domain' => 'business',
                'class' => 'StreetVC\BorrowerBundle\Document\Industry',
                'query_builder' => function (DocumentRepository $repository) {
                    return $repository->createQueryBuilder();
//                        ->field('id')->equals(new \MongoRegex("/^\\d\\d\$/"));
                }
        ));

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
        return 'business_legal';
    }
}
