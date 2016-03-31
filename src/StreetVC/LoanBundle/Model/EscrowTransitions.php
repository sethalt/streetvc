<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 10/2/2014
 * Time: 12:45 PM
 */

namespace StreetVC\LoanBundle\Model;


class EscrowTransitions {

    const
        REQUEST = 'request',
        OPEN_CONFIRMED = 'open_confirmed',
        FUNDED = 'funded',
        PAY_ORIGINATION_FEE = 'pay_origination_fee',
//        CLOSE = 'close',
        DISBURSE = 'disburse',
        CLOSE_CONFIRMED = 'close_confirmed',
//        DISBURSEMENT_CONFIRMED = 'disbursement_confirmed',
        SCHEDULE_PAYMENTS = 'schedule_payments',
        REPAYING = 'repaying',
        REPAID = 'repaid',
        CANCEL = 'cancel',
        CANCEL_CONFIRMED = 'cancel_confirmed'
    ;


}
