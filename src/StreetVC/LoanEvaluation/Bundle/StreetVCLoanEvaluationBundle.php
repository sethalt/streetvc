<?php

namespace StreetVC\LoanEvaluation\Bundle;

use StreetVC\LoanEvaluation\Bundle\DependencyInjection\LoanEvaluationExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class StreetVCLoanEvaluationBundle extends Bundle
{
    public function getExtension()
    {
        return new LoanEvaluationExtension();
    }
}
