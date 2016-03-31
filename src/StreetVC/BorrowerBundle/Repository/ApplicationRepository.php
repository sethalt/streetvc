<?php
namespace StreetVC\BorrowerBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentRepository;
use StreetVC\BorrowerBundle\Document\BorrowerApplication;

class ApplicationRepository extends DocumentRepository
{
    public function findActiveByUserId($id)
    {
        return $this->findOneBy(['user'=>$id, 'state'=>'active']);
    }
    public function findPreapprovedByUserId($id)
    {
        return $this->findOneBy(['user'=>$id, 'state'=>'preapproved']);
    }
    public function findApprovedByUserId($id)
    {
        return $this->findOneBy(['user'=>$id, 'state'=>'approved']);
    }
}
