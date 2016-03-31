<?php
namespace Sd\UserBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use JMS\Serializer\Annotation\Expose;
use FOS\UserBundle\Model\User;

/** @MongoDB\Document */
class BaseUser extends User
{
     /** @MongoDB\Id(strategy="auto") @JMS\Expose */
    protected $id;

    public function getId() {
        return $this->id;
    }

    /** @MongoDB\String @MongoDB\Index(unique=true) @Expose */
    protected $username;

    /** @MongoDB\String */
    protected $usernameCanonical;

    /** @MongoDB\String @MongoDB\Index(unique=true) @Expose */
    protected $email;

    /** @MongoDB\String */
    protected $address;

    /** @MongoDB\String */
    protected $emailCanonical;

    /** #MongoDB\EmbedMany(targetDocument="Sd\BaseBundle\Document\PhoneNumber") */
    protected $phone_numbers;

    /** @MongoDB\Boolean */
    protected $enabled;

    /** @MongoDB\String */
    protected $salt;

    /** @MongoDB\String */
    protected $password;

    /** @MongoDB\Date */
    protected $lastLogin;

    /** @MongoDB\Boolean */
    protected $locked;

    /** @MongoDB\Boolean */
    protected $expired;

    /** @MongoDB\Date */
    protected $expiresAt;

    /** @MongoDB\String */
    protected $confirmationToken;

    /** @MongoDB\Date */
    protected $passwordRequestedAt;

    /** @MongoDB\Boolean */
    protected $credentialsExpired;

    /** @MongoDB\Date */
    protected $credentialsExpireAt;

    /** @MongoDB\Hash */
    protected $roles;

    /** @MongoDB\ReferenceMany(targetDocument="BaseGroup") */
    protected $groups;

    public function __toString()
    {
        return $this->username;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public static function getGenderList()
    {
        return array('m' => 'male', 'f' => 'female');
    }
}
