<?php
namespace StreetVC\BorrowerBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use StreetVC\LenderBundle\Document\LoanCommitment;
class LoanCommitmentManager
{

    protected $om;
    protected $class;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->class = '\StreetVC\LenderBundle\Document\LoanCommitment';
        $this->repository = $this->om->getRepository('StreetVC\LenderBundle\Document\LoanCommitment');
    }

    public function findBy($criteria)
    {
        return $this->repository->findBy($criteria);
    }

    public function update(LoanCommitment $commitment, $andFlush)
    {
        $this->om->persist($commitment);
        if ($andFlush) {
            $this->om->flush();
        }
        return $commitment;
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    protected function getRepository()
    {
        return $this->repository;
    }

}