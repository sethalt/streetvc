<?php
namespace StreetVC\LoanBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentRepository;
use StreetVC\LoanBundle\Document\Escrow;

class EscrowRepository extends DocumentRepository
{
    public function findOneByBancboxId($id)
    {
        return $this->findOneBy(['bancbox_id'=>$id]);
    }

    /**
     * @return ArrayCollection|Escrow
     */
    public function getExpiringEscrows()
    {
        $now = new \DateTime();
        $expiring = $this->createQueryBuilder()
            ->field('close_date')->lt($now)
            ->field('state')->equals('open')
            ->field('expired')->equals(false)
            ->getQuery()->execute();
        return $expiring;
    }

}
