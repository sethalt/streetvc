<?php
namespace StreetVC\BorrowerBundle\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as JMS;
use StreetVC\Model\IssuerInterface;
use StreetVC\UserBundle\Document\User;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Sd\BaseBundle\Document\Address;
use StreetVC\TransactionBundle\Document\BankAccount;
use StreetVC\LoanBundle\Document\LoanRequest;
use StreetVC\LoanBundle\Document\Escrow;
use StreetVC\BorrowerBundle\Document\MonthlyFinancial;

/** @MongoDB\Document(repositoryClass="StreetVC\BorrowerBundle\Repository\ApplicationRepository")
 * @JMS\ExclusionPolicy("all")
 */
class BorrowerApplication 
{

    /**
     * @JMS\Expose
     * @MongoDB\Id(strategy="AUTO")
     */
    protected $id;

    /**
     * @MongoDB\String
     * @var string
     * @JMS\Expose
     */
    protected $state;

    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\UserBundle\Document\User", simple=true, inversedBy="borrower_applications")
     */
    protected $user;

    /**
     * @MongoDB\String
     * @JMS\Expose
     */
    protected $legal_name;

    /**
     * @MongoDB\String
     * @Assert\Length( min = "9", max = "9", exactMessage = "Tax ID (EIN) must be 9 digits in length" )
     * @var string Tax ID (EIN)
     * @JMS\Expose
     */
    protected $tax_id;

    /**
     * @MongoDB\String
     * @var string Registration State
     * @JMS\Expose
     */
    protected $registration_state;

    /**
     * @MongoDB\EmbedOne(targetDocument="Sd\BaseBundle\Document\Address")
     * @var Address Primary business address
     * @JMS\Expose
     */
    protected $business_address;
    
    /**
     * @MongoDB\EmbedOne(targetDocument="Sd\BaseBundle\Document\Address")
     * @var Address Primary business address
     * @JMS\Expose
     */
    protected $personal_address;

    /**
     * @MongoDB\String
     * @Assert\Length( min = "10", max = "20", minMessage = "Phone number must be at least 10 digits long", maxMessage = "Phone number must be at most 20 digits long" )
     * @var string Business phone number
     * @JMS\Expose
     */
    protected $business_phone;
    
    /**
     * @MongoDB\String
     * @Assert\Length( min = "10", max = "20", minMessage = "Phone number must be at least 10 digits long", maxMessage = "Phone number must be at most 20 digits long" )
     * @var string Business phone number
     * @JMS\Expose
     */
    protected $personal_phone;
    
    /**
     * #MinAge(age="18")
     * @MongoDB\Date
     * @var DateTime $date_of_birth
     */
    protected $date_of_birth;

    /**
     * @MongoDB\String
     * @Assert\Choice(callback = "getLegalStructures")
     * @var string Corporate Structure
     * @JMS\Expose
     */
    protected $legal_structure;

    /**
     * @MongoDB\Boolean
     * @var bool Business is primarily bricks-and-mortar
     * @JMS\Expose
     */
    protected $bricks_and_mortar;

    /**
     * @MongoDB\Boolean
     * @Assert\Type(type="bool", message="Value must be true or false")
     * @var bool Does your business have revenue?
     * @JMS\Expose
     */
    protected $has_revenue;

    /**
     * @MongoDB\Float
     * @var float Annual Revenue
     * @JMS\Expose
     */
    protected $annual_revenue;
    
    /**
     * @MongoDB\Float
     * @var float net_profit
     * @JMS\Expose
     */
    protected $net_profit;
    
    /**
     * @var array|ArrayCollection
     * @MongoDB\EmbedMany(targetDocument="MonthlyFinancial")
     * @JMS\Expose()
     */
    protected $past_cashflow;
    
    /**
     * @var array|ArrayCollection
     * @MongoDB\EmbedMany(targetDocument="MonthlyFinancial")
     * @JMS\Expose()
     */
    protected $past_revenue;
    
    /**
     * @MongoDB\Boolean
     * @var bool Cyclical business?
     * @JMS\Expose
     */
    protected $cyclical_business;
    
    /**
     * @MongoDB\Int
     * @var int number_employees
     * @JMS\Expose
     */
    protected $number_employees;
    
    /**
     * @MongoDB\Boolean
     * @var bool Cyclical business?
     * @JMS\Expose
     */
    protected $credit_facilities;
    
    /**
     * @MongoDB\Float
     * @var float  
     * @JMS\Expose
     */
    protected $cf_largest_amount_outstanding;
    
    /**
     * @MongoDB\Float
     * @var float  
     * @JMS\Expose
     */
    protected $cf_amount_due;
    
    /**
     * @MongoDB\Date
     * @var date
     * @JMS\Expose
     */
    protected $cf_final_payment_date;
    
    /**
     * @MongoDB\Float
     * @var float
     * @JMS\Expose
     */
    protected $assets_real_estate_value;
    
    /**
     * @MongoDB\Float
     * @var float
     * @JMS\Expose
     */
    protected $assets_real_estate_equity;
    
    /**
     * @MongoDB\Float
     * @var float
     * @JMS\Expose
     */
    protected $assets_equipment;
    
    /**
     * @MongoDB\Float
     * @var float
     * @JMS\Expose
     */
    protected $assets_inventory;
    
    /**
     * @MongoDB\Float
     * @var float
     * @JMS\Expose
     */
    protected $assets_accounts_receivable;
    
    /**
     * @MongoDB\Float
     * @var float
     * @JMS\Expose
     */
    protected $assets_cash;
    
    
    /**
     * @MongoDB\Boolean
     * @var bool own or lease location?
     * @JMS\Expose
     */
    protected $own_lease_location;
    
    /**
     * @MongoDB\Int
     * @var int remaining term of lease (years) 
     * @JMS\Expose
     */
    protected $remaining_term_of_lease;

    /**
     * @MongoDB\Boolean
     * @var bool Does your business have cashflow?
     * @JMS\Expose
     */
    protected $has_cashflow;

    /**
     * @MongoDB\Int
     * @var int cashflow
     * @JMS\Expose
     */
    protected $cashflow;
    
    /**
     * @MongoDB\Int
     * @var int number_years_profitable 
     * @JMS\Expose
     */
    protected $number_years_profitable;
   
    /**
     * @MongoDB\ReferenceOne(targetDocument="StreetVC\BorrowerBundle\Document\Industry", simple=true)
     * @var string Industry Primary industry by NAICS
     * @JMS\Expose
     */
    protected $industry;

    /**
     * @MongoDB\String
     * @Assert\Url()
     * @JMS\Expose
     */
    protected $website;
    
    /**
     * @MongoDB\String
     * @JMS\Expose
     */
    protected $email;

    /**
     * @MongoDB\String
     * intro video on why lenders should contribute $ to initiative
     * @JMS\Expose
     */
    protected $video;

    /**
     * short written business bio
     * @MongoDB\String
     * @var string description of business
     * @JMS\Expose
     */
    protected $bio;

    /**
     * @MongoDB\String
     * @var string Additional info for prospective lenders
     * @JMS\Expose
     */
    protected $additional_info;
    
    /**
     * @MongoDB\String
     * @JMS\Expose
     */
    protected $dbas;
    
    /**
     * @MongoDB\String
     * @JMS\Expose
     * @var string
     */
    protected $year_established;
    
    /**
     * @MongoDB\Int
     * @JMS\Expose
     */
    protected $number_locations;
    
    /**
     * @MongoDB\String
     * @JMS\Expose
     */
    protected $time_at_current_location; 

    /**
     * @MongoDB\String
     * @var string average margin from sale of service/good
     * @JMS\Expose
     */
    protected $margin;
    
    /**
     * @MongoDB\Float
     * @var float
     * @JMS\Expose
     */
    protected $income;
    
    /**
     * @MongoDB\Float
     * @var float
     * @JMS\Expose
     */
    protected $guarantor_business_ownership;
    
    /**
     * @MongoDB\Int
     * @JMS\Expose
     */
    protected $funding_goal;
    
    /**
     * @MongoDB\Int
     * @JMS\Expose
     * term of loan in months
     */
    protected $term;
    
    /**
     * @MongoDB\String
     * @var string loan purpose 
     * @JMS\Expose
     */
    protected $use_of_loan_proceeds;
    
    

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
    
    public function __construct()
    {
        $this->past_cashflow = new ArrayCollection();
        $this->past_revenue = new ArrayCollection();
        $this->created_at = new DateTime();
        $this->updated_at = new DateTime();
        $this->date_of_birth = new DateTime();
        $this->business_address = new Address();
        $this->personal_address = new Address();
    }

    public function __toString()
    {
        return $this->getLegalName();
    }


    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getTitle()
    {
        return $this->getLegalName();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLegalName()
    {
        return $this->legal_name ?: '';
    }

    public function setLegalName($name)
    {
        $this->legal_name = $name;
        return $this;
    }

    public function getTaxId()
    {
        return $this->tax_id;
    }

    public function setTaxId($tax_id)
    {
        $this->tax_id = $tax_id;
    }

    public function setRegistrationState($state)
    {
        $this->registration_state =$state;
    }

    public function getRegistrationState()
    {
        return $this->registration_state;
    }

    public function getVentureFunding()
    {
        return $this->venture_funding;
    }
    public function setVentureFunding($venture_funding)
    {
        $this->venture_funding = $venture_funding;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Set industry
     *
     * @param string $industry
     * @return self
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;
        return $this;
    }

    /**
     * Get industry
     *
     * @return string $industry
     */
    public function getIndustry()
    {
        return $this->industry;
    }
    
    /**
     * Set business_address
     *
     * @param Address $business_address
     * @return self
     */
    public function setBusinessAddress(Address $address)
    {
        $this->business_address = $address;
        return $this;
    }
    
    /**
     * Get business_address
     *
     * @return Address $business_address
     */
    public function getBusinessAddress()
    {
        return $this->business_address;
    }

    /**
     * Set personal_address
     *
     * @param Address $personal_address
     * @return self
     */
    public function setPersonalAddress(Address $address)
    {
        $this->personal_address = $address;
        return $this;
    }

    /**
     * Get personal_address
     *
     * @return Address $personal_address
     */
    public function getPersonalAddress()
    {
        return $this->personal_address;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return self
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * Get website
     *
     * @return string $website
     */
    public function getWebsite()
    {
        return $this->website;
    }
    
    
    /**
     * Set email 
     *
     * @param string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    
    /**
     * Get email 
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set video
     *
     * @param string $video
     * @return self
     */
    public function setVideo($video)
    {
        $this->video = $video;
        return $this;
    }

    /**
     * Get video
     *
     * @return string $video
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set bio
     *
     * @param string $bio
     * @return self
     */
    public function setBio($bio)
    {
        $this->bio = $bio;
        return $this;
    }

    /**
     * Get bio
     *
     * @return string $bio
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set margin
     *
     * @param string $margin
     * @return self
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;
        return $this;
    }

    /**
     * Get margin
     *
     * @return string $margin
     */
    public function getMargin()
    {
        return $this->margin;
    }

    public static function getLegalStructures()
    {
        $choices = array('Sole Proprietor', 'LLC', 'LP', 'C Corporation', 'S Corporation', 'Other');
        return array_combine($choices, $choices);
    }

    /**
     * Set phone
     *
     * @param string $personal_phone
     * @return self
     */
    public function setPersonalPhone($phone)
    {
        $this->personal_phone = $phone;
        return $this;
    }
    
    /**
     * Get phone
     *
     * @return string $personal_phone
     */
    public function getPersonalPhone()
    {
        return $this->personal_phone;
    }
    
    /**
     * Set phone
     *
     * @param string $business_phone
     * @return self
     */
    public function setBusinessPhone($phone)
    {
        $this->business_phone = $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string $business_phone
     */
    public function getBusinessPhone()
    {
        return $this->business_phone;
    }

    /**
     * Set legalStructure
     *
     * @param string $legalStructure
     * @return self
     */
    public function setLegalStructure($legalStructure)
    {
        $this->legal_structure = $legalStructure;
        return $this;
    }

    /**
     * Get legalStructure
     *
     * @return string $legalStructure
     */
    public function getLegalStructure()
    {
        return $this->legal_structure;
    }

    /**
     * Set bricksAndMortar
     *
     * @param boolean $bricksAndMortar
     * @return self
     */
    public function setBricksAndMortar($bricksAndMortar)
    {
        $this->bricks_and_mortar = $bricksAndMortar;
        return $this;
    }

    /**
     * Get bricksAndMortar
     *
     * @return boolean $bricksAndMortar
     */
    public function getBricksAndMortar()
    {
        return $this->bricks_and_mortar;
    }

    /**
     * Set has revenue
     *
     * @param bool $hasRevenue
     * @return self
     */
    public function setHasRevenue($has_revenue)
    {
        $this->has_revenue = $has_revenue;
        return $this;
    }

    /**
     * Get has revenue
     *
     * @return bool $annualRevenue
     */
    public function getHasRevenue()
    {
        return $this->has_revenue;
    }

    /**
     * Set annual revenue
     *
     * @param int $annualRevenue
     * @return self
     */
    public function setAnnualRevenue($annualRevenue)
    {
        $this->annual_revenue = $annualRevenue;
        return $this;
    }

    /**
     * Get annualRevenue
     *
     * @return int $annualRevenue
     */
    public function getAnnualRevenue()
    {
        return $this->annual_revenue;
    }


    /**
     * Set hasCashflow
     *
     * @param bool $hasCashflow
     * @return self
     */
    public function setHasCashflow($hasCashflow)
    {
        $this->has_cashflow = $hasCashflow;
        return $this;
    }

    /**
     * Get hasCashflow
     *
     * @return bool $hasCashflow
     */
    public function getHasCashflow()
    {
        return $this->has_cashflow;
    }

    /**
     * Set cashflow
     *
     * @param int $cashflow
     * @return self
     */
    public function setCashflow($cashflow)
    {
        $this->cashflow = $cashflow;
        return $this;
    }

    /**
     * GetCashflow
     *
     * @return int $Cashflow
     */
    public function getcashflow()
    {
        return $this->cashflow;
    }

    /**
     * Set additionalInfo
     *
     * @param string $additionalInfo
     * @return self
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->additional_info = $additionalInfo;
        return $this;
    }

    /**
     * Get additionalInfo
     *
     * @return string $additionalInfo
     */
    public function getAdditionalInfo()
    {
        return $this->additional_info;
    }
    
    
    
    /**
     * Set dbas
     *
     * @param string $dbas
     * @return self
     */
    public function setDbas($dbas)
    {
        $this->dbas = $dbas;
        return $this;
    }
    
    /**
     * Get dbas
     *
     * @return string $dbas
     */
    public function getDbas()
    {
        return $this->dbas;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }
    
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
        return $this;
    }

    public function getLastName()
    {
        return $this->last_name;
    }
    
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
        return $this;
    }
    
    
    /**
     * @return PastCashflow|ArrayCollection
     */
    public function getPastCashflow()
    {
        return $this->past_cashflow;
    }
    
    public function addPastCashflow(MonthlyFinancial $past_cashflow)
    {
        $this->past_cashflow->add($past_cashflow);
    }
    
    public function setPastCashflow($past_cashflow)
    {
        $this->past_cashflow = $past_cashflow;
        return $this;
    }
    
    /**
     * @return PastRevenue|ArrayCollection
     */
    public function getPastRevenue()
    {
        return $this->past_revenue;
    }
    
    public function addPastRevenue(MonthlyFinancial $past_revenue)
    {
        $this->past_revenue->add($past_revenue);
    }
    
    public function getYearEstablished()
    {
        return $this->year_established;
    }
    
    public function setYearEstablished($year_established)
    {
        $this->year_established = $year_established;
        return $this;
    }

    public function getNumberLocations()
    {
        return $this->number_locations;
    }
    
    public function setNumberLocations($number_locations)
    {
        $this->number_locations = $number_locations;
        return $this;
    }

    public function getTimeAtCurrentLocation()
    {
        return $this->time_at_current_location;
    }
    
    public function setTimeAtCurrentLocation($time_at_current_location)
    {
        $this->time_at_current_location = $time_at_current_location;
        return $this;
    }

    public function getNetProfit()
    {
        return $this->net_profit;
    }
    
    public function setNetProfit($net_profit)
    {
        $this->net_profit = $net_profit;
        return $this;
    }

    public function getNumberEmployees()
    {
        return $this->number_employees;
    }
    
    public function setNumberEmployees($number_employees)
    {
        $this->number_employees = $number_employees;
        return $this;
    }

    public function getOwnLeaseLocation()
    {
        return $this->own_lease_location;
    }
    
    public function setOwnLeaseLocation($own_lease_location)
    {
        $this->own_lease_location = $own_lease_location;
        return $this;
    }
    
    public function getRemainingTermOfLease()
    {
        return $this->remaining_term_of_lease;
    }
    
    public function setRemainingTermOfLease($remaining_term_of_lease)
    {
        $this->remaining_term_of_lease = $remaining_term_of_lease;
        return $this;
    }

    public function getCyclicalBusiness()
    {
        return $this->cyclical_business;
    }
    
    public function setCyclicalBusiness($cyclical_business)
    {
        $this->cyclical_business = $cyclical_business;
        return $this;
    }
    
    public function getCreditFacilities()
    {
        return $this->credit_facilities;
    }
    
    public function setCreditFacilities($credit_facilities)
    {
        $this->credit_facilities = $credit_facilities;
        return $this;
    }


    public function getCfLargestAmountOutstanding()
    {
        return $this->cf_largest_amount_outstanding;
    }
    
    public function setCfLargestAmountOutstanding($cf_largest_amount_outstanding)
    {
        $this->cf_largest_amount_outstanding = $cf_largest_amount_outstanding;
        return $this;
    }

    public function getCfAmountDue()
    {
        return $this->cf_amount_due;
    }
    
    public function setCfAmountDue($cf_amount_due)
    {
        $this->cf_amount_due = $cf_amount_due;
        return $this;
    }

    public function getCfFinalPaymentDate()
    {
        return $this->cf_final_payment_date;
    }
    
    public function setCfFinalPaymentDate($cf_final_payment_date)
    {
        $this->cf_final_payment_date = $cf_final_payment_date;
        return $this;
    }

    public function getAssetsRealEstateValue()
    {
        return $this->assets_real_estate_value;
    }
    
    public function setAssetsRealEstateValue($assets_real_estate_value)
    {
        $this->assets_real_estate_value = $assets_real_estate_value;
        return $this;
    }

    public function getAssetsRealEstateEquity()
    {
        return $this->assets_real_estate_equity;
    }
    
    public function setAssetsRealEstateEquity($assets_real_estate_equity)
    {
        $this->assets_real_estate_equity = $assets_real_estate_equity;
        return $this;
    }

    public function getAssetsEquipment()
    {
        return $this->assets_equipment;
    }
    
    public function setAssetsEquipment($assets_equipment)
    {
        $this->assets_equipment = $assets_equipment;
        return $this;
    }

    public function getAssetsInventory()
    {
        return $this->assets_inventory;
    }
    
    public function setAssetsInventory($assets_inventory)
    {
        $this->assets_inventory = $assets_inventory;
        return $this;
    }

    public function getAssetsAccountsReceivable()
    {
        return $this->assets_accounts_receivable;
    }
    
    public function setAssetsAccountsReceivable($assets_accounts_receivable)
    {
        $this->assets_accounts_receivable = $assets_accounts_receivable;
        return $this;
    }

    public function getAssetsCash()
    {
        return $this->assets_cash;
    }
    
    public function setAssetsCash($assets_cash)
    {
        $this->assets_cash = $assets_cash;
        return $this;
    }
    
    /**
     * @param DateTime $date
     * @return $this
     */
    public function setDateOfBirth(DateTime $date = null)
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
     * @MongoDB\String
     * @JMS\Expose
     */
    protected $first_name;
    
    /**
     * @MongoDB\String
     * @JMS\Expose
     */
    protected $last_name;
    
    
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
    
    public function getIncome()
    {
        return $this->income;
    }
    
    public function setIncome($income)
    {
        $this->income = $income;
        return $this;
    }
    
    public function getGuarantorBusinessOwnership()
    {
        return $this->guarantor_business_ownership;
    }
    
    public function setGuarantorBusinessOwnership($guarantor_business_ownership)
    {
        $this->guarantor_business_ownership = $guarantor_business_ownership;
        return $this;
    }
    public function getFundingGoal()
    {
        return $this->funding_goal;
    }
    
    /**
     * Set fundingGoal
     *
     * @param int $fundingGoal
     * @return self
     */
    public function setFundingGoal($fundingGoal)
    {
        $this->funding_goal = $fundingGoal;
        $this->overfunding_amount = $fundingGoal;
        return $this;
    }
    
    public function setUseOfLoanProceeds($use_of_loan_proceeds)
    {
        $this->use_of_loan_proceeds = $use_of_loan_proceeds;
        return $this;
    }
    
    public function getUseOfLoanProceeds()
    {
        return $this->use_of_loan_proceeds;
    }
    
    public function setTerm($term)
    {
        $this->term = (int) $term;
        return $this;
    }
    
    public function getTerm()
    {
        return $this->term;
    }
    
    public function setNumberYearsProfitable($number_years_profitable)
    {
        $this->number_years_profitable = (int) $number_years_profitable;
        return $this;
    }
    
    public function getNumberYearsProfitable()
    {
        return $this->number_years_profitable;
    }
    
}
