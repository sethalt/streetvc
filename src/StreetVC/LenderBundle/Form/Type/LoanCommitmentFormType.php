<?php
namespace StreetVC\LenderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use StreetVC\LoanBundle\Document\Escrow;
use JMS\DiExtraBundle\Annotation as DI;
use StreetVC\UserBundle\Document\User;

class LoanCommitmentFormType extends AbstractType
{

    protected $escrow;

    public function __construct(Escrow $escrow, User $user) {
        $this->escrow = $escrow;
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
        $accounts = [];
        foreach ($this->user->getLender()->getLinkedAccounts() as $account) {
            $accounts[$account->getId()] = $account->render();
        }
        */
        $builder->add('amount', 'integer', 
            array('label'=>'minimum amount $'.$this->escrow->getMinimumFundingAmount().', maximum $'.$this->escrow->getMaximumFundingAmount()), 
            [
            'attr'=> [
                'min'=>$this->escrow->getMinimumFundingAmount(),
                'max'=>$this->escrow->getMaximumFundingAmount()
            ]
        ]);
//        $builder->add('account_id', 'choice', array('choices'=>$accounts, 'mapped'=>false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults( array(
            'data_class'=>'StreetVC\LenderBundle\Document\LoanCommitment',
        ));
    }

    public function getName()
    {
        return 'loan_commitment';
    }
}