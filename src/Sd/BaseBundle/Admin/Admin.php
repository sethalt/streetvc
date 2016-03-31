<?php

namespace Sd\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin as BaseAdmin;

class Admin extends BaseAdmin
{
    /*
    public function getSubject()
    {
        if ($this->subject === null && $this->request) {
            $id = $this->request->get($this->getIdParameter());
            $this->subject = $this->getObject($id);
        }

        return $this->subject;
    }
    */
}
