<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/4/14
 * Time: 11:18 AM
 */

namespace StreetVC\BancboxInvest\Service;


use Monolog\Logger;
use StreetVC\BancboxInvest\Event\WebhookEvent;
use StreetVC\BancboxInvest\Event\WebhookEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class WebhookService
 * @package StreetVC\BancboxInvest\Service
 */
class WebhookService {

    private $dispatcher;
    private $logger;
    private $api_key;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param Logger $logger
     * @param $api_key
     */
    public function __construct(EventDispatcherInterface $dispatcher, Logger $logger, $api_key)
    {
        $this->logger = $logger;
        $this->api_key = $api_key;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param $action
     * @return bool
     * Verify that action passed in webhook params is known.
     */
    public function validateAction($action)
    {
        if (!WebhookEvents::isValidAction($action)) {
            $msg = "Received unknown webhook action: $action";
            $this->logger->error($msg);
//            throw new \InvalidArgumentException($msg); // just log
        }
        return true;
    }

    /**
     * @param $apiKey
     * @throws UnauthorizedHttpException
     * Verify that api key provided in webhook params matches that assigned by Bancbox
     */
    public function verifyKey($apiKey)
    {
        $actualKey = $this->api_key;
        if ($apiKey != $actualKey) {
            throw new UnauthorizedHttpException("Invalid api key");
        }
    }

    /**
     * @param $action
     * @param $params
     * Notify listeners that a webhook request was received
     */
    public function notify($action, $params)
    {
        $event = new WebhookEvent($action, $params);
        $this->dispatcher->dispatch("bancbox.webhook", $event);
    }

}
