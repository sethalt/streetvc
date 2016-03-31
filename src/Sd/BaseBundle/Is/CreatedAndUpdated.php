<?php
namespace Sd\BaseBundle\Is;

interface CreatedAndUpdated
{
    public function getCreated();
    public function setCreated(\DateTime $datetime = null);
    public function getModified();
    public function setModified(\DateTime $datetime = null);
}