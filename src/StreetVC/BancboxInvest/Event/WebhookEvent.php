<?php
/**
 * Created by PhpStorm.
 * User: dao
 * Date: 7/28/14
 * Time: 6:04 PM
 */

namespace StreetVC\BancboxInvest\Event;

use Symfony\Component\EventDispatcher\Event;

class WebhookEvent extends Event {

    private $params;
    private $result;
    private $action;

    public function __construct($action, array $params)
    {
        $this->action = $action;
        $this->params = $params;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getParam($param)
    {
        return $this->params[$param];
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }


}
