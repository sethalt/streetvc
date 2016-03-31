<?php
namespace StreetVC\LoanBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class RepaymentScheduleRepository extends DocumentRepository
{

    public function findOneByBancboxId($id)
    {
        return $this->findOneBy(['bancbox_id'=>$id]);
    }

    public function getUnprocessedRepayments()
    {
        $qb = $this->createQueryBuilder();
        return $qb->getQuery()->execute();
    }
}
