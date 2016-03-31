<?php
namespace StreetVC\UserBundle\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use FOS\UserBundle\Model\User as UserModel;
use Sd\BaseBundle\Document\Address;
use StreetVC\LenderBundle\Document\Lender;
use Symfony\Component\Validator\Constraints as Assert;
use StreetVC\BorrowerBundle\Document\Business;
use StreetVC\BorrowerBundle\Document\BorrowerApplication;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;



/**
 * @MongoDB\Document(repositoryClass="StreetVC\UserBundle\Repository\UserRepository")
 * @MongoDB\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 * @MongoDBUnique(fields="email", message="The email address has already been used")
 * @MongoDBUnique(fields="username", message="The username address has already been used, please choose a new username")
 */
class User extends UserModel
{
    /**
     * @MongoDB\Id(strategy="AUTO")
     * @var \MongoId
     */
    protected $id;

    /**
     * @MongoDB\String
     * @JMS\Expose
     * @var string
     */
    protected $first_name;

    /**
     * @MongoDB\String
     * @JMS\Expose
     * @var string
     */
    protected $last_name;

    /**
     * @MongoDB\Date
     * @JMS\Expose
     * @var DateTime
     */
    protected $created_at;

    /**
     * @MongoDB\Date
     * @JMS\Expose
     * @var DateTime
     */
    protected $updated_at;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\BorrowerBundle\Document\Business", simple=true, mappedBy="user")
     * @var Business
     */
    protected $business;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\LenderBundle\Document\Lender", simple=true)
     * @JMS\Expose
     * @var Lender
     */
    protected $lender;
    
    /**
     * @MongoDB\ReferenceMany(targetDocument="StreetVC\BorrowerBundle\Document\BorrowerApplication", simple=true, mappedBy="user")
     * @var borrower_applications
     */
    protected $borrower_applications;

    /**
     * @MongoDB\String
     * Assert\Length(min="11",max="11")
     * #Assert\NotBlank()
     * #Assert\Regex(
     *      pattern="/^(\d{3}-\d{2}-\d{4}|XXX-XX-XXXX)$/",
     *      htmlPattern="^\d{3}-\d{2}-\d{4}$"
     * )
     * @var string
     */
    protected $social_security_number;

    /**
     * @MongoDB\EmbedOne(targetDocument="Sd\BaseBundle\Document\Address")
     * @var Address
     */
    protected $address;

    /**
     * #MinAge(age="18")
     * @MongoDB\Date
     * @var DateTime $date_of_birth
     */
    protected $date_of_birth;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $phone_number;
    
    /**
     * @MongoDB\Boolean
     * @var boolean 
     */
    protected $cashflow_positive;

    /**
     * @MongoDB\ReferenceMany()
     */
    protected $following;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $last_ip;

    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        parent::__construct();
        $this->created_at = new DateTime();
        $this->updated_at = new DateTime();
        $this->address = new Address();
    }
    
    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->setUsername($email);
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function getFullName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function getFirstName()
    {
        return $this->first_name ?: '';
    }

    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
        return $this;
    }

    public function getLastName()
    {
        return $this->last_name ?: '';
    }

    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
        return $this;
    }

    public function setUpdatedAt(DateTime $time)
    {
        $this->updated_at = $time;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setBusiness(Business $business)
    {
        $this->business= $business;
        $business->setUser($this);
        return $this;
    }

    public function getOrCreateBusiness()
    {
        if (!$business = $this->business) {
            $business = new Business();
            $business->setUser($this);
            $this->setBusiness($business);
        }
        return $business;
    }

    /**
     * @return Business
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * @param DateTime $date
     * @return $this
     */
    public function setDateOfBirth(DateTime $date)
    {
        $this->date_of_birth = $date;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDateOfBirth()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth;
    }

    /**
     * @return null|string
     */
    public function getDob()
    {
        if (!$dob = $this->getDateOfBirth()) {
            return null;
        }
        return $dob->format('Y-m-d');
    }

    public function getSocialSecurityNumber()
    {
        return $this->social_security_number;
    }

    public function setSocialSecurityNumber($number)
    {
        $this->social_security_number = $number;
        return $this;
    }

    public function getPhoneNumber()
    {
        return $this->phone_number;
    }
    public function setPhoneNumber($number)
    {
        $this->phone_number = $number;
        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }
    public function setAddress(Address $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return Lender
     */
    protected function createLender()
    {
        if (!$this->lender) {
            $this->lender = new Lender($this);
        }
        return $this->lender;
    }

    /**
     * @return Lender
     */
    public function getLender()
    {
        if (!$this->lender) {
            $this->lender = $this->createLender();
        }
        return $this->lender;
    }

    public function setLender(Lender $lender)
    {
        $this->lender = $lender;
    }

    public function getInvestorId()
    {
        if (!$this->getLender()) {
            return null;
        }
        return $this->getLender()->getBancboxId();
    }

    public function getIssuer()
    {
        return $this->business;
    }

    public function getIssuerId()
    {
        if ($business = $this->getBusiness()) {
            return null;
        }
        return $business->getBancboxId();
    }

    public function updateIp($ip)
    {
        $this->last_ip = $ip;
    }

    public function getLastIp()
    {
        return $this->last_ip;
    }
    
    public function setCashflowPositive($cashflow_positive)
    {
        $this->cashflow_positive = $cashflow_positive;
        return $this;
    }
    
    public function getCashflowPositive()
    {
        return $this->cashflow_positive;
    }
    
    public function getBorrowerApplications()
    {
        return $this->borrower_applications;
    }
    
    /*
    public function getFollowing()
    {
        return $this->following;
    }

    public function addFollowing(ActorInterface $actor)
    {
        $this->getFollowing()->add($actor);
        return $this;
    }

    public function removeFollowing(ActorInterface $actor)
    {
        $this->getFollowing()->removeElement($actor);
        return $this;
    }
    */

}
