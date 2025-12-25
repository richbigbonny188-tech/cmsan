<?php
/* --------------------------------------------------------------
   NewsletterSubscription.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface NewsletterSubscription
 *
 * @category   System
 * @package    NewsletterSubscription
 */
class NewsletterSubscription implements NewsletterSubscriptionInterface
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $email;
    
    /**
     * @var int
     */
    protected $customerId;
    
    /**
     * @var int
     */
    protected $customerStatus;
    
    /**
     * @var string
     */
    protected $firstName;
    
    /**
     * @var string
     */
    protected $lastName;
    
    /**
     * @var int
     */
    protected $mailStatus;
    
    /**
     * @var string
     */
    protected $mailKey;
    
    /**
     * @var DateTime
     */
    protected $subscriptionDate;
    
    /**
     * @var string
     */
    protected $ipAddress;
    
    
    /**
     * NewsletterSubscription constructor.
     *
     * @param \IdType                 $id
     * @param \CustomerEmailInterface $email
     * @param \IdType                 $customerId
     * @param \IntType                $customerStatus
     * @param \StringType             $firstName
     * @param \StringType             $lastName
     * @param \IntType                $mailStatus
     * @param \StringType             $mailKey
     * @param \DateTime               $subscriptionDate
     * @param \StringType             $ipAddress
     */
    public function __construct(
        IdType $id,
        CustomerEmailInterface $email,
        IdType $customerId,
        IntType $customerStatus,
        StringType $firstName,
        StringType $lastName,
        IntType $mailStatus,
        StringType $mailKey,
        DateTime $subscriptionDate,
        StringType $ipAddress
    ) {
        $this->id               = $id->asInt();
        $this->email            = (string)$email;
        $this->customerId       = $customerId->asInt();
        $this->customerStatus   = $customerStatus->asInt();
        $this->firstName        = $firstName->asString();
        $this->lastName         = $lastName->asString();
        $this->mailStatus       = $mailStatus->asInt();
        $this->mailKey          = $mailKey->asString();
        $this->subscriptionDate = $subscriptionDate;
        $this->ipAddress        = $ipAddress->asString();
    }
    
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    
    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
    
    
    /**
     * @return int
     */
    public function getCustomerStatus()
    {
        return $this->customerStatus;
    }
    
    
    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    
    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    
    
    /**
     * @return int
     */
    public function getMailStatus()
    {
        return $this->mailStatus;
    }
    
    
    /**
     * @return string
     */
    public function getMailKey()
    {
        return $this->mailKey;
    }
    
    
    /**
     * @return \DateTime
     */
    public function getSubscriptionDate()
    {
        return $this->subscriptionDate;
    }
    
    
    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }
}