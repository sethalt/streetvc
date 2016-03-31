<?php

namespace StreetVC\BorrowerBundle\Event\Listener;

class LoanCommitmentListener
{

    public function onLoanCommitment($event)
    {
        $commitment = $event->getLoanCommitment();
        $request = $event->getLoanRequest();
        $amountFunded = $request->getAmountFunded();
        $amountFunded += $commitment->getAmount();
        $this->odm->persist($request);
        $this->odm->flush();


    }
}