<?php
/**
 * Created by PhpStorm.
 * User: dao
 * Date: 7/29/14
 * Time: 9:33 AM
 */

namespace StreetVC\BancboxInvest\Event\Listener;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use StreetVC\BancboxInvest\Event\WebhookEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class WebhookListener
 * @DI\Service("bancbox.webhook.listener")
 * @DI\Tag("monolog.logger", attributes = { "channel" = "bancbox" })
 * @package StreetVC\BancboxInvest\Event\Listener
 */
class WebhookListener {

    private $logger;

    /**
     * @param Logger $logger
     * @DI\InjectParams
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param WebhookEvent $event
     * @param $name
     * @param EventDispatcherInterface $dispatcher
     * @DI\Observe("bancbox.webhook")
     */
    public function onWebhookReceived(WebhookEvent $event, $name, EventDispatcherInterface $dispatcher)
    {
        $action = $event->getAction();
        $params = $this->cleanParams($event->getParams());
        $context = ['time' => microtime(), 'params' => json_encode($params) ];
        $this->logger->info("bancbox.webhook.received $action: " . json_encode($params), $context);
        $eventName = "bancbox.webhook.$action";
        try {
            $event = $dispatcher->dispatch($eventName, $event);
        } catch (\Exception $e) {
            $this->logger->error("bancbox.webhook.exception ". $e->getMessage(), $context);
        }
    }

    private function cleanParams($params)
    {
        // unset secret platform key
        unset($params['cfp_apiKey']);
        return $params;
    }

}
