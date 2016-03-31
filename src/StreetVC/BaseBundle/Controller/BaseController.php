<?php

namespace StreetVC\BaseBundle\Controller;


class BaseController extends \Sd\BaseBundle\Controller\BaseController
{

    /**
     * @param $repository
     * @return \Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected function getRepository($repository)
    {
        return $this->get('odm')->getRepository($repository);
    }
}
