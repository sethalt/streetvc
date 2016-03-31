<?php
namespace StreetVC\BancboxInvest\BancboxInvestBundle\Model;

use GuzzleHttp\ToArrayInterface;

class VerifyChallengeDeposit implements ToArrayInterface
{
    use GuzzleHttp\HasDataTrait;

    private $challenge_id;
    private $challenge_deposit_1;
    private $challenge_deposit_2;

    public function __construct($challenge_id, $challenge_deposit_1, $challenge_deposit_2)
    {
        $this->data = [
            'challenge_id' => $challenge_id,
            'challenge_deposit_1' => $challenge_deposit_1,
            'challenge_deposit_2' => $challenge_deposit_2
        ];
        /*
        $this->challenge_id = $challenge_id;
        $this->challenge_deposit_1 = $challenge_deposit_1;
        $this->challenge_deposit_2 = $challenge_deposit_2;
        */
    }

}