<?php
/**
 * User: dao
 * Date: 7/28/14
 * Time: 5:56 PM
 */

namespace StreetVC\BancboxInvest\Event;


class WebhookEvents
{

    const
        PROCEEDS_ACTIVITY_FAILURE_NOTIFICATION = "bancbox.webhook.PROCEEDS_ACTIVITY_FAILURE_NOTIFICATION",
        CHALLENGE_DEPOSIT_FAILED = "bancbox.webhook.CHALLENGE_DEPOSIT_FAILED",
        BILLABLE_ACTIVITY_NOTIFICATION = "bancbox.webhook.BILLABLE_ACTIVITY_NOTIFICATION",
        OPEN_ESCROW = "bancbox.webhook.OPEN_ESCROW",
        FUNDING_ACTIVITY_FAILURE_NOTIFICATION = "bancbox.webhook.FUNDING_ACTIVITY_FAILURE_NOTIFICATION",
        CANCELLED_ESCROW = "bancbox.webhook.CANCELLED_ESCROW",
        CLOSE_ESCROW = "bancbox.webhook.CLOSE_ESCROW",
        INVESTOR_BALANCE_CHANGE = "bancbox.webhook.INVESTOR_BALANCE_CHANGE",
        ISSUER_BALANCE_CHANGE = "bancbox.webhook.ISSUER_BALANCE_CHANGE",
        TRANSFER_FUNDS_SUCCESSFUL = "bancbox.webhook.TRANSFER_FUNDS_SUCCESSFUL",
        FUND_ESCROW = "bancbox.webhook.FUND_ESCROW"
    ;

    public static function isValidAction($action)
    {
        return self::hasConstant($action);
    }

    public static function getConstants()
    {
        return self::getReflection()->getConstants();
    }

    public static function hasConstant($constant)
    {
        return self::getReflection()->hasConstant($constant);
    }

    public static function getConstant($constant)
    {
        return self::getReflection()->getConstant($constant);
    }

    /**
     * @return \ReflectionClass
     */
    protected static function getReflection()
    {
        return new \ReflectionClass(__CLASS__);
    }
}
