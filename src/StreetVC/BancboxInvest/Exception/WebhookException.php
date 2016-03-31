<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/26/2014
 * Time: 12:53 PM
 */

namespace StreetVC\BancboxInvest\Exception;


use StreetVC\BancboxInvest\Event\WebhookEvent;

class WebhookException extends \Exception {

    /**
     * @var WebhookEvent
     */
    private $webhook_event;

    /**
     * @param WebhookEvent $event
     */
    public function setWebhookEvent(WebhookEvent $event)
    {
        $this->webhook_event = $event;
    }

    /**
     * @return WebhookEvent
     */
    public function getWebhookEvent()
    {
        return $this->webhook_event;
    }
}
