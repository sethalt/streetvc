<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/6/14
 * Time: 5:24 PM
 */

namespace StreetVC\BancboxInvest\Event;


use GuzzleHttp\Command\Model;
use GuzzleHttp\Event\CompleteEvent;
use Symfony\Component\EventDispatcher\Event;

class BancboxRequestEvent extends Event {

    private $client;
    private $response;
    private $request;

    public function __construct(CompleteEvent $event)
    {
        $this->request = $event->getRequest();
        $this->response = $event->getResponse();
        $this->client = $event->getClient();
    }


}
