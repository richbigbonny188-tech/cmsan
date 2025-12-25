<?php
/* --------------------------------------------------------------
   CustomerWriteService.inc.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerWriteServiceInterface');

/**
 * Class CustomerWriteService
 *
 * This class provides methods for creating and deleting customer data
 *
 * @category   System
 * @package    Customer
 * @implements CustomerWriteServiceInterface
 */
class CustomerWriteService implements CustomerWriteServiceInterface
{
    /**
     * Address book service.
     * @var AddressBookServiceInterface
     */
    protected $addressBookService;
    
    /**
     * Customer repository.
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    
    /**
     * Customer service settings.
     * @var CustomerServiceSettingsInterface
     */
    protected $customerServiceSettings;
    
    /**
     * VAT number validator.
     * @var VatNumberValidatorInterface
     */
    protected $vatNumberValidator;
    
    /**
     * @var \DeleteHistoryWriteService
     */
    protected $deleteHistory;
    
    
    /**
     * Constructor of the class CustomerService.
     *
     * @param AddressBookServiceInterface      $addressBookService      Address book service.
     * @param CustomerRepositoryInterface      $customerRepository      Customer repository.
     * @param CustomerServiceSettingsInterface $customerServiceSettings Customer service settings.
     * @param VatNumberValidatorInterface      $vatNumberValidator      VAT number validator.
     */
    public function __construct(
        AddressBookServiceInterface $addressBookService,
        CustomerRepositoryInterface $customerRepository,
        CustomerServiceSettingsInterface $customerServiceSettings,
        VatNumberValidatorInterface $vatNumberValidator,
        DeleteHistoryWriteService $deleteHistoryWriteService
    ) {
        $this->addressBookService      = $addressBookService;
        $this->customerRepository      = $customerRepository;
        $this->customerServiceSettings = $customerServiceSettings;
        $this->vatNumberValidator      = $vatNumberValidator;
        $this->deleteHistory           = $deleteHistoryWriteService;
    }
    
    
    /**
     * Creates a new customer with the given parameters.
     *
     * Will create a guest account if $password is an instance of CustomerGuestPassword.
     *
     * @param CustomerEmailInterface       $email           Customer's E-Mail address.
     * @param CustomerPasswordInterface    $password        Customer's password.
     * @param DateTime                     $dateOfBirth     Customer's date of birth.
     * @param CustomerVatNumberInterface   $vatNumber       Customer's VAT number.
     * @param CustomerCallNumberInterface  $telephoneNumber Customer's telephone number.
     * @param CustomerCallNumberInterface  $faxNumber       Customer's fax number.
     * @param AddressBlockInterface        $addressBlock    Customer's address.
     * @param KeyValueCollection           $addonValues     Customer's additional values.
     * @param CustomerNumberInterface|null $customerNumber  Customer's number.
     *
     * @return Customer Created customer.
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException On invalid arguments.
     *
     * TODO Replaced by Vat Check
     */
    public function createNewRegistree(
        CustomerEmailInterface $email,
        CustomerPasswordInterface $password,
        DateTime $dateOfBirth,
        CustomerVatNumberInterface $vatNumber,
        CustomerCallNumberInterface $telephoneNumber,
        CustomerCallNumberInterface $faxNumber,
        AddressBlockInterface $addressBlock,
        KeyValueCollection $addonValues,
        ?CustomerNumberInterface $customerNumber = null
    ) {
        $isGuest = $password instanceof CustomerGuestPassword;
        
        $this->customerRepository->deleteGuestByEmail($email);
        
        if ($this->customerRepository->getRegistreeByEmail($email) !== null) {
            throw new UnexpectedValueException('E-Mail already used in existing customer.');
        }
        
        /* @var Customer $customer */
        $customer = $this->customerRepository->getNewCustomer();
        $customer->setGuest($isGuest);
        $statusId = $isGuest ? $this->customerServiceSettings->getDefaultGuestStatusId() : $this->customerServiceSettings->getDefaultCustomerStatusId();
        $customer->setStatusId($statusId);
        $number = (strlen($customerNumber ?? '') !== 0) ? $customerNumber : MainFactory::create('CustomerNumber',
                                                                                                 (string)$customer->getId());
        $customer->setCustomerNumber($number);
        $customer->setGender($addressBlock->getGender());
        $customer->setFirstname($addressBlock->getFirstname());
        $customer->setLastname($addressBlock->getLastname());
        $customer->setCompany($addressBlock->getCompany());
        $customer->setEmail($email);
        if ($isGuest === false) {
            $customer->setPassword($password);
        }
        $customer->setDateOfBirth($dateOfBirth);
        $customer->setTelephoneNumber($telephoneNumber);
        $customer->setFaxNumber($faxNumber);
        
        // import addressBlock data into empty default address
        $this->addressBookService->updateAddress($addressBlock, $customer->getDefaultAddress());
        
        $vatNumberStatus = $this->vatNumberValidator->getVatNumberStatusCodeId($vatNumber,
                                                                               $addressBlock->getCountry()->getId(),
                                                                               $isGuest);
        $customer->setVatNumber($vatNumber);
        $customer->setVatNumberStatus($vatNumberStatus);
        
        if ($isGuest === false || $this->customerServiceSettings->getMoveOnlyIfNoGuest()) {
            $vatCustomerStatus = $this->vatNumberValidator->getCustomerStatusId($vatNumber,
                                                                                $addressBlock->getCountry()->getId(),
                                                                                $isGuest);
            $customer->setStatusId($vatCustomerStatus);
        }
        
        $customer->addAddonValues($addonValues);
        
        $this->customerRepository->store($customer);
        
        return $customer;
    }
    
    
    /**
     * Creates a new guest account with the given parameters.
     *
     * @param CustomerEmailInterface       $email           Customer's E-Mail address.
     * @param DateTime                     $dateOfBirth     Customer's date of birth.
     * @param CustomerVatNumberInterface   $vatNumber       Customer's VAT number.
     * @param CustomerCallNumberInterface  $telephoneNumber Customer's telephone number.
     * @param CustomerCallNumberInterface  $faxNumber       Customer's fax number.
     * @param AddressBlockInterface        $addressBlock    Customer's address.
     * @param KeyValueCollection           $addonValues     Customer's additional values.
     * @param CustomerNumberInterface|null $customerNumber  Customer's number.
     *
     * @return Customer Created guest customer.
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException On invalid arguments.
     * @deprecated use createNewRegistree() with an instance of CustomerGuestPassword
     */
    public function createNewGuest(
        CustomerEmailInterface $email,
        DateTime $dateOfBirth,
        CustomerVatNumberInterface $vatNumber,
        CustomerCallNumberInterface $telephoneNumber,
        CustomerCallNumberInterface $faxNumber,
        AddressBlockInterface $addressBlock,
        KeyValueCollection $addonValues,
        ?CustomerNumberInterface $customerNumber = null
    ) {
        $this->customerRepository->deleteGuestByEmail($email);
        
        if ($this->customerRepository->getRegistreeByEmail($email) != null) {
            throw new UnexpectedValueException('E-Mail already used in existing customer.');
        }
        
        /* @var Customer $customer */
        $customer = $this->customerRepository->getNewCustomer();
        $customer->setGuest(true);
        $customer->setStatusId($this->customerServiceSettings->getDefaultGuestStatusId());
        $number = (strlen($customerNumber) != 0) ? $customerNumber : MainFactory::create('CustomerNumber',
                                                                                                 (string)$customer->getId());
        $customer->setCustomerNumber($number);
        $customer->setGender($addressBlock->getGender());
        $customer->setFirstname($addressBlock->getFirstname());
        $customer->setLastname($addressBlock->getLastname());
        $customer->setEmail($email);
        $customer->setDateOfBirth($dateOfBirth);
        $customer->setTelephoneNumber($telephoneNumber);
        $customer->setFaxNumber($faxNumber);
        
        // import addressBlock data into empty default address
        $this->addressBookService->updateAddress($addressBlock, $customer->getDefaultAddress());
        
        $vatNumberStatus = $this->vatNumberValidator->getVatNumberStatusCodeId($vatNumber,
                                                                               $addressBlock->getCountry()->getId(),
                                                                               true);
        $customer->setVatNumber($vatNumber);
        $customer->setVatNumberStatus($vatNumberStatus);
        
        if ($this->customerServiceSettings->getMoveOnlyIfNoGuest()) {
            $vatCustomerStatus = $this->vatNumberValidator->getCustomerStatusId($vatNumber,
                                                                                $addressBlock->getCountry()->getId(),
                                                                                true);
            $customer->setStatusId($vatCustomerStatus);
        }
        $customer->addAddonValues($addonValues);
        
        $this->customerRepository->store($customer);
        
        return $customer;
    }
    
    
    /**
     * Deletes the customer with the provided ID.
     *
     * @param IdType $customerId Customer's ID.
     */
    public function deleteCustomerById(IdType $customerId)
    {
        $this->customerRepository->deleteCustomerById($customerId);
        $this->deleteHistory->reportDeletion(DeletedId::create((string)$customerId->asInt()),
                                             DeleteHistoryScope::customers());
    }
    
    
    /**
     * Updates customer data.
     *
     * @param CustomerInterface $customer Customer.
     *
     * @return CustomerInterface Updated customer.
     *
     * TODO check if the new email address is used by another record
     */
    public function updateCustomer(CustomerInterface $customer)
    {
        $vatNumberStatus = $this->vatNumberValidator->getVatNumberStatusCodeId($customer->getVatNumber(),
                                                                               $customer->getDefaultAddress()
                                                                                   ->getCountry()
                                                                                   ->getId(),
                                                                               false);
        $customer->setVatNumberStatus($vatNumberStatus);
        $this->customerRepository->store($customer);
        
        return $customer;
    }
}
