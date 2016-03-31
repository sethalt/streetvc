<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 9/2/2014
 * Time: 3:02 PM
 */

namespace StreetVC\TransactionBundle\Tests\Event\Listener;


use StreetVC\BancboxInvest\Event\WebhookEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BalanceChangeListenerTest extends KernelTestCase
{
    public function setUp()
    {

    }


    public function testOnIssuerBalanceChange()
    {
        $params = [ 'investor_id' => '1', 'updated_amount' => 1000 ];
        $event = new WebhookEvent('ISSUER_BALANCE_CHANGE', $params);

    }

    public function testOnInvestorBalanceChange()
    {
        $params = [ 'issuer_id' => '1', 'updated_amount' => 1000 ];
        $event = new WebhookEvent('INVESTOR_BALANCE_CHANGE', $params);

    }
}
