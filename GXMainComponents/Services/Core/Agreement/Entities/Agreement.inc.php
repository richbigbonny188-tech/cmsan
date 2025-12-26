<?php
/* --------------------------------------------------------------
   Agreement.inc.php 2018-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Agreement
 *
 * @category   System
 * @package    Agreement
 * @subpackage Entities
 */
class Agreement implements AgreementInterface
{
    /**
     * @var \AgreementRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var IdType
     */
    protected $id = 0;
    
    /**
     * @var \DateTime
     */
    protected $dateAdded;
    
    /**
     * @var \DateTime
     */
    protected $lastModifiedDate;
    
    /**
     * @var
     */
    protected $customer;
    
    /**
     * @var \StringType
     */
    protected $ipAddress = '';
    
    /**
     * @var \NonEmptyStringType
     */
    protected $text = '';
    
    /**
     * @var \StringType
     */
    protected $legalTextVersion = '';
    
    /**
     * @var \IdType
     */
    protected $contentGroup = 0;
    
    /**
     * @var \IdType
     */
    protected $languageId = 0;
    
    
    /**
     * Agreement constructor.
     *
     * @param \AgreementRepositoryInterface $repository .
     */
    public function __construct(AgreementRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->dateAdded  = $this->lastModifiedDate = new DateTime();
        $this->ipAddress  = new StringType('');
    }
    
    
    /**
     * Stores the agreement.
     *
     * @return $this|\Agreement Same instance for chained method calls.
     */
    public function store()
    {
        $this->repository->store($this);
        
        return $this;
    }
    
    
    /**
     * Deletes the agreement.
     *
     * @return $this|\Agreement Same instance for chained method calls.
     */
    public function delete()
    {
        $this->repository->delete($this);
        
        return $this;
    }
    
    
    /**
     * Sets the agreement id.
     *
     * @param \IdType $id The agreement id to be set.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setId(IdType $id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    
    /**
     * Returns the agreement id.
     *
     * @return IdType Agreement id.
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Sets the date of the agreement.
     *
     * @param \DateTime $dateTime Date and time.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setDateAdded(DateTime $dateTime)
    {
        $this->dateAdded = $dateTime;
        
        return $this;
    }
    
    
    /**
     * Returns the date and time of the agreement.
     *
     * @return \DateTime Date and time of the agreement.
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }
    
    
    /**
     * Sets the last modified date of the agreement.
     *
     * @param \DateTime $dateTime Date and time.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setLastModifiedDate(DateTime $dateTime)
    {
        $this->lastModifiedDate = $dateTime;
        
        return $this;
    }
    
    
    /**
     * Returns the last modified date of the agreement.
     *
     * @return \DateTime Date and time of the agreement.
     */
    public function getLastModifiedDateTime()
    {
        return $this->lastModifiedDate;
    }
    
    
    /**
     * Sets the customers to the agreement.
     *
     * @param \AgreementCustomerInterface $customer Customer.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setCustomer(AgreementCustomerInterface $customer)
    {
        $this->customer = $customer;
        
        return $this;
    }
    
    
    /**
     * Returns the customers of the agreement.
     *
     * @return \AgreementCustomerInterface Customer.
     */
    public function getCustomer()
    {
        return $this->customer;
    }
    
    
    /**
     * Sets the IP address of the customer which confirmed the agreement.
     *
     * @param StringType $ipAddress
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        
        return $this;
    }
    
    
    /**
     * Returns the IP address of the customer which confirmed the agreement.
     *
     * @return StringType
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }
    
    
    /**
     * Sets the text of the agreement.
     *
     * @param \NonEmptyStringType $text Agreement text.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setText(NonEmptyStringType $text)
    {
        $this->text = $text;
        
        return $this;
    }
    
    
    /**
     * Returns the text of the agreement.
     *
     * @return \NonEmptyStringType
     */
    public function getText()
    {
        return $this->text;
    }
    
    
    /**
     * Sets the version of the legal text.
     *
     * @param StringType $legalTextVersion Version of the legal text.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setLegalTextVersion(StringType $legalTextVersion)
    {
        $this->legalTextVersion = $legalTextVersion;
        
        return $this;
    }
    
    
    /**
     * Returns the version of the legal text.
     *
     * @return StringType
     */
    public function getLegalTextVersion()
    {
        return $this->legalTextVersion;
    }
    
    
    /**
     * Sets the content group of the legal text.
     *
     * @param \IdType $contentGroup
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setContentGroup($contentGroup)
    {
        $this->contentGroup = $contentGroup;
        
        return $this;
    }
    
    
    /**
     * Returns the content group ID of the legal text.
     *
     * @return \IdType
     */
    public function getContentGroup()
    {
        return $this->contentGroup;
    }
    
    
    /**
     * Sets the language ID of the agreement.
     *
     * @param \IdType $languageId Language ID.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setLanguageId(IdType $languageId)
    {
        $this->languageId = $languageId;
        
        return $this;
    }
    
    
    /**
     * Returns the language ID of the agreement.
     *
     * @return \IdType Language ID.
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }
}