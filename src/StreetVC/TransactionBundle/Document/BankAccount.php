<?php
namespace StreetVC\TransactionBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @MongoDB\EmbeddedDocument
 */
class BankAccount
{
    const CHECKING = 'CHECKING';
    const SAVING = 'SAVING';

    const UNVERIFIED = 'UNVERIFIED';
    const VERIFYING = 'VERIFYING';
    const VERIFIED = 'VERIFIED';
    const FAILED = 'FAILED';

    /**
     * @MongoDB\Id(strategy="NONE")
     * @var \MongoId
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $account_holder;

    /** @MongoDB\String */
    protected $name; // bank name

    /**
     * @MongoDB\String
     */
    protected $type = 'CHECKING'; // account type

    /**
     * http://www.routingnumbers.info/api/name.json?rn=075906016
     * @MongoDB\String
     * @Assert\NotBlank
     * @Assert\Length(min="9", max="9", exactMessage="Routing Number must be exactly 9 characters")
     */
    protected $routing_number;

    /**
     * @MongoDB\String
     * @Assert\NotBlank
     * @Assert\Length(min="9", max="9", exactMessage="Account number must be exactly 9 characters")
     */
    protected $account_number;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $bancbox_id;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $bancbox_challenge_id;

    /**
     * @var float
     * @MongoDB\Float
     */
    protected $bancbox_challenge_1;

    /**
     * @var float
     * @MongoDB\Float
     */
    protected $bancbox_challenge_2;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $state;

    /**
     * @MongoDB\Boolean
     * @var bool
     */
    protected $verified;

    /**
     * @MongoDB\Date
     * @var \DateTime
     */
    protected $verified_on;

    /**
     * @MongoDB\Float
     * @var float
     */
    protected $balance;

    static function getTypes()
    {
        return array('SAVING' => 'SAVING', 'CHECKING' => 'CHECKING');
    }

    static function getStates() {
        return array(self::UNVERIFIED => self::UNVERIFIED, self::VERIFYING => self::VERIFYING, self::VERIFIED => self::VERIFIED, self::FAILED);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function render()
    {
        return $this->getName() . ': ' . $this->account_number;
    }

    public function __construct()
    {
        $this->id = (string) new \MongoId();
        $this->verified = false;
        $this->state = self::UNVERIFIED;
        $this->balance = 0;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function setAccountHolder($name)
    {
        $this->account_holder = $name;
    }
    public function getAccountHolder()
    {
        return $this->account_holder;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $type = strtoupper($type);
        if (!in_array($type, self::getTypes())) {
            throw new \InvalidArgumentException("Argument $type is not in " . implode(', ', self::getTypes()));
        }
        $this->type = $type;
    }

    public function getAccountNumber()
    {
        return $this->account_number;
    }

    public function setAccountNumber($number)
    {
        /*
        if (!is_string($number)) {
            throw new \InvalidArgumentException("Account number must be a string.");
        }
        */
        $this->account_number = (string) $number;
        return $this;
    }

    public function getRoutingNumber()
    {
        return $this->routing_number;
    }

    public function setRoutingNumber($number)
    {
        /*
        if (!is_string($number)) {
            throw new \InvalidArgumentException("Routing number must be a string.");
        }
        */
        $this->routing_number = (string) $number;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getBancboxId()
    {
        return $this->bancbox_id;
    }

    public function setBancboxId($id)
    {
        $this->bancbox_id = $id;
        return $this;
    }

    public function setChallengeId($id)
    {
        $this->bancbox_challenge_id = $id;
        return $this;
    }

    public function getChallengeId()
    {
        return $this->bancbox_challenge_id;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function isVerified()
    {
        return $this->verified;
    }

    public function getVerified() {
        return $this->verified;
    }

    public function setVerified($bool) {
        $this->verified = $bool;
        $this->verified_on = new \DateTime();
    }

    public function getVerifiedOn()
    {
        return $this->verified_on;
    }

    /**
     * @return string
     */
    public function getBancboxChallengeId()
    {
        return $this->bancbox_challenge_id;
    }

    /**
     * @param string $bancbox_challenge_id
     */
    public function setBancboxChallengeId($bancbox_challenge_id)
    {
        $this->bancbox_challenge_id = $bancbox_challenge_id;
    }

    /**
     * @return float
     */
    public function getBancboxChallenge1()
    {
        return $this->bancbox_challenge_1;
    }

    /**
     * @param float $bancbox_challenge_1
     */
    public function setBancboxChallenge1($bancbox_challenge_1)
    {
        $this->bancbox_challenge_1 = $bancbox_challenge_1;
    }

    /**
     * @return float
     */
    public function getBancboxChallenge2()
    {
        return $this->bancbox_challenge_2;
    }

    /**
     * @param float $bancbox_challenge_2
     */
    public function setBancboxChallenge2($bancbox_challenge_2)
    {
        $this->bancbox_challenge_2 = $bancbox_challenge_2;
    }
}
