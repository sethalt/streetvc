<?php
namespace StreetVC\BancboxInvest\BancboxInvestBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use StreetVC\TransactionBundle\Document\BankAccount;

trait BancboxAccountTrait
{
    /**
     * @MongoDB\String
     * @var string
     */
    private $bancbox_id;

    /**
     * @MongoDB\EmbedOne(targetDocument="StreetVC\TransactionBundle\Document\BankAccount")
     * @var BankAccount
     */
    private $internal_account;

    /**
     * @MongoDB\EmbedMany(targetDocument="StreetVC\TransactionBundle\Document\BankAccount")
     * @var ArrayCollection|BankAccount
     */
    private $linked_accounts;

    /**
     * @MongoDB\String
     * @var string
     */
    private $status;

    /**
     * @MongoDB\Float
     * @var float
     */
    private $balance;

    /**
     * @MongoDB\Float
     * @JMS\Expose()
     */
    protected $current_balance;

    /**
     * @MongoDB\Float
     * @JMS\Expose()
     */
    protected $pending_balance;

    /**
     * @param $amount
     * @throws \Exception
     */
    public function addFunds($amount)
    {
        $this->checkAmount($amount);
        $this->current_balance += $amount;
    }

    /**
     * @param $amount
     * @param $account
     * @throws \Exception
     */
    public function withdrawFunds($amount, $account=null)
    {
        $this->checkAmount($amount);
        if ($amount > $this->getBalance()) {
            throw new \Exception("Withdrawal would result in negative balance.");
        }
        $this->current_balance -= $amount;
    }

    /**
     * @param $amount
     * @throws \Exception
     */
    protected function checkAmount($amount)
    {
        if ($amount <= 0) {
            throw new \Exception("Amount must be positive.");
        }
    }

    /**
     * @return float
     * @deprecated
     */
    public function getBalance()
    {
        return $this->getCurrentBalance();
    }

    /**
     * @param $balance
     * @return string
     * @deprecated
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    public function getCurrentBalance()
    {
        if ($this->current_balance == null) {
            $this->current_balance = 0;
        }
        return $this->current_balance;
    }

    public function setCurrentBalance($balance)
    {
        $this->current_balance = $balance;
    }

    public function setPendingBalance($balance)
    {
        $this->pending_balance = $balance;
    }

    public function getPendingBalance()
    {
        return $this->pending_balance;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getReferenceId()
    {
        return (string) $this->id;
    }

    public function getBancboxId()
    {
        return $this->bancbox_id;
    }

    public function setBancboxId($id)
    {
        $this->bancbox_id = $id;
    }

    public function setInternalAccount(BankAccount $account)
    {
        $this->internal_account = $account;
        return $this;
    }

    public function getInternalAccount()
    {
        if (!$this->internal_account) {
            $this->internal_account = new BankAccount();
        }
        return $this->internal_account;
    }

    public function getInternalAccountId()
    {
        if ($this->internal_account instanceof BankAccount) {
            return $this->internal_account->getId();
        }
    }

    public function getLinkedAccounts()
    {
        if (!$this->linked_accounts) {
            $this->linked_accounts = new ArrayCollection();
        }
        return $this->linked_accounts;
    }

    public function addLinkedAccount(BankAccount $account)
    {
        $this->getLinkedAccounts()->add($account);
    }

    /**
     * @param $account_id
     * @throws \Exception
     * @return BankAccount
     */
    public function getAccountById($account_id)
    {
        if ($account_id == $this->getInternalAccountId()) {
            return $this->getInternalAccount();
        }
        foreach ($this->getLinkedAccounts() as $account) {
            if ($account->getId() == $account_id) {
                return $account;
            }
        }
        throw new \Exception("Attempt to access invalid account");
    }
}
