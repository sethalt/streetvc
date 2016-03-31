<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 10/20/2014
 * Time: 5:49 PM
 */

namespace StreetVC\LoanBundle\Model;


class PaymentCalculator {

    /**
     * @param float $principal amount to be repaid
     * @param int $term duration of repayment in months
     * @param float $rate interest rate
     * @return float monthly payment due
     */
    public static function calculateTermPayment($principal, $term, $rate)
    {
        $term_rate = $rate * 100 / 12;
        $factor = $term_rate / (1 - pow((1 + $term_rate), -$term));
        $term_payment = $principal * $factor;
        return $term_payment;
    }

}
