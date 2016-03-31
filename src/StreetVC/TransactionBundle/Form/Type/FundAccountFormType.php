<?php
namespace StreetVC\TransactionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @DI\FormType("fund_account")
 * @author dao
 */
class FundAccountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $getChoices = function($options) {
            $choices = [];
            foreach ($options['accounts'] as $account) {
                $choices[$account->getId()] = (string) $account;
            }
            return $choices;
        };
        $builder->add('account', 'choice', array('choices'=>$choices()));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'StreetVC\BancboxInvest\BancboxInvestBundle\Model\FundAccountClickthrough'
        ]);
    }

    public function getName()
    {
        return 'fund_account';
    }
}