<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 10/10/2014
 * Time: 12:55 PM
 */

namespace StreetVC\UserBundle\Repository;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentRepository;
use StreetVC\UserBundle\Document\User;

class UserRepository extends DocumentRepository {

    /**
     * @return ArrayCollection|User
     */
    public function getLenderUsers()
    {
        $users = $this->createQueryBuilder()
            ->field('lender')->exists(true)
            ->getQuery()->execute();
        return $users;
    }
    
}
