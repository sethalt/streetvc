<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/5/14
 * Time: 11:30 AM
 */

//namespace StreetVC\LoanEvaluation\Tests\Service;


use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\LoanEvaluation\Service\LoanEvaluatorStub;

class LoanEvaluatorStubTest extends \PHPUnit_Framework_TestCase {

    public function testEvaluationHasRate()
    {
        $request = $this->getLoanRequest();
        $stub = new LoanEvaluatorStub();
        $evaluation = $stub->evaluate($request);
        $this->assertNotNull($evaluation->getRate());
    }

    private function getLoanRequest()
    {
        $request = new LoanRequest();
        $request->setTerm(12);
        return $request;
    }
}
