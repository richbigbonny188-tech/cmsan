<?php
/* --------------------------------------------------------------
   CustomerServiceFactory.inc.php 2018-07-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCustomerServiceFactory');

/**
 * Class CustomerServiceFactory
 *
 * Factory class for all needed customer data.
 *
 * @category System
 * @package  Customer
 * @extends  AbstractCustomerServiceFactory
 */
class CustomerServiceFactory extends AbstractCustomerServiceFactory
{
    /**
     * Query builder.
     * @var CI_DB_query_builder
     */
    protected $ciDatabaseQueryBuilder;
    
    /**
     * @var \CustomerConfigurationProvider
     */
    protected $configurationProvider;
    
    
    /**
     * CustomerServiceFactory constructor.
     *
     * @param CI_DB_query_builder $ciDatabaseQueryBuilder Query builder.
     */
    public function __construct(CI_DB_query_builder $ciDatabaseQueryBuilder)
    {
        $this->ciDatabaseQueryBuilder = $ciDatabaseQueryBuilder;
    }
    
    
    /**
     * Returns the country service.
     *
     * @return CountryService Country service.
     */
    public function getCountryService()
    {
        $customerCountryRepo     = $this->_getCustomerCountryRepository();
        $customerCountryZoneRepo = $this->_getCustomerCountryZoneRepository();
        
        $countryService = MainFactory::create('CountryService', $customerCountryRepo, $customerCountryZoneRepo);
        
        return $countryService;
    }
    
    
    /**
     * Returns the customer service.
     *
     * @return CustomerService Customer service.
     */
    public function getCustomerService()
    {
        $customerReadService  = $this->createCustomerReadService();
        $customerWriteService = $this->createCustomerWriteService();
        
        $customerService = MainFactory::create('CustomerService', $customerReadService, $customerWriteService);
        
        return $customerService;
    }
    
    
    /**
     * Creates a customer read service object.
     *
     * @return CustomerReadService Customer read service.
     */
    public function createCustomerReadService()
    {
        $customerRepository = $this->_getCustomerRepository();
        
        $customerReadService = MainFactory::create('CustomerReadService', $customerRepository);
        
        return $customerReadService;
    }
    
    
    /**
     * Creates a customer service object.
     *
     * @return CustomerService Customer service.
     */
    public function createCustomerWriteService()
    {
        $addressBookService      = $this->getAddressBookService();
        $customerRepository      = $this->_getCustomerRepository();
        $customerServiceSettings = $this->_getCustomerServiceSettings();
        $vatValidator            = MainFactory::create('VatNumberValidator');
        
        $customerWriteService = MainFactory::create('CustomerWriteService',
                                                    $addressBookService,
                                                    $customerRepository,
                                                    $customerServiceSettings,
                                                    $vatValidator,
                                                    DeleteHistoryServiceFactory::writeService());
        
        return $customerWriteService;
    }
    
    
    /**
     * Returns the address book service.
     *
     * @return AddressBookService Address book service.
     */
    public function getAddressBookService()
    {
        $addressRepository  = $this->_getCustomerAddressRepository();
        $addressBookService = MainFactory::create('AddressBookService', $addressRepository);
        
        return $addressBookService;
    }
    
    
    /**
     * Returns the customer registration input validator service.
     *
     * @return CustomerRegistrationInputValidatorService Customer registration input validator service.
     */
    public function getCustomerRegistrationInputValidatorService()
    {
        return $this->_getCustomerInputValidatorServiceByValidatorName('CustomerRegistrationInputValidatorService');
    }
    
    
    /**
     * Returns the customer account input validator.
     *
     * @return CustomerAccountInputValidator Customer account input validator.
     */
    public function getCustomerAccountInputValidator()
    {
        return $this->_getCustomerInputValidatorServiceByValidatorName('CustomerAccountInputValidator');
    }
    
    
    /**
     * Returns the database query builder.
     *
     * @return CI_DB_query_builder Query builder.
     */
    public function getDatabaseQueryBuilder()
    {
        return $this->ciDatabaseQueryBuilder;
    }
    
    
    /**
     * Returns the customer factory.
     *
     * @return CustomerFactory Customer factory.
     *
     * TODO Inject CustomerFactory
     */
    protected function _getCustomerFactory()
    {
        $customerFactory = MainFactory::create('CustomerFactory');
        
        return $customerFactory;
    }
    
    
    /**
     * Creates a customer repository object.
     *
     * @return CustomerRepository Customer repository.
     */
    protected function _getCustomerRepository()
    {
        $customerWriter    = $this->_getCustomerWriter();
        $customerReader    = $this->_getCustomerReader();
        $customerDeleter   = $this->_getCustomerDeleter();
        $addressRepository = $this->_getCustomerAddressRepository();
        $customerFactory   = $this->_getCustomerFactory();
        $addonValueService = $this->_getAddonValueService();
        
        $repository = MainFactory::create('CustomerRepository',
                                          $customerWriter,
                                          $customerReader,
                                          $customerDeleter,
                                          $addressRepository,
                                          $customerFactory,
                                          $addonValueService);
        
        return $repository;
    }
    
    
    /**
     * Returns the customer input validator.
     *
     * @return CustomerAddressInputValidator Customer input validator.
     */
    public function getCustomerAddressInputValidatorService()
    {
        return $this->_getCustomerInputValidatorServiceByValidatorName('CustomerAddressInputValidator');
    }
    
    
    /**
     * Creates a customer country repository object.
     *
     * @return CustomerCountryRepository Customer country repository.
     */
    protected function _getCustomerCountryRepository()
    {
        $reader = $this->_getCustomerCountryReader();
        $repo   = MainFactory::create('CustomerCountryRepository', $reader);
        
        return $repo;
    }
    
    
    /**
     * Creates a customer country zone repository object.
     *
     * @return CustomerCountryZoneRepository Customer country zone repository.
     */
    protected function _getCustomerCountryZoneRepository()
    {
        $reader          = $this->_getCustomerCountryZoneReader();
        $customerFactory = $this->_getCustomerFactory();
        $repo            = MainFactory::create('CustomerCountryZoneRepository', $reader, $customerFactory);
        
        return $repo;
    }
    
    
    /**
     * Creates a customer address repository object.
     *
     * @return CustomerAddressRepository Customer address repository.
     */
    protected function _getCustomerAddressRepository()
    {
        $writer     = $this->_getCustomerAddressWriter();
        $reader     = $this->_getCustomerAddressReader();
        $deleter    = $this->_getCustomerAddressDeleter();
        $factory    = $this->_getCustomerFactory();
        $repository = MainFactory::create('CustomerAddressRepository', $writer, $deleter, $reader, $factory);
        
        return $repository;
    }
    
    
    /**
     * Returns customer input validator service by validator name.
     *
     * @param string $inputValidatorName Name of input validator service.
     *
     * @return object Found customer input validator service.
     */
    protected function _getCustomerInputValidatorServiceByValidatorName($inputValidatorName)
    {
        $customerService = $this->getCustomerService();
        $countryService  = $this->getCountryService();
        
        $settings = $this->_getInputValidatorSettings();
        
        $countryRepo        = $this->_getCustomerCountryRepository();
        $countryZoneRepo    = $this->_getCustomerCountryZoneRepository();
        $vatNumberValidator = MainFactory::create('VatNumberValidator');
        
        return MainFactory::create($inputValidatorName,
                                   $customerService,
                                   $countryService,
                                   $settings,
                                   $countryRepo,
                                   $countryZoneRepo,
                                   $vatNumberValidator);
    }
    
    
    /**
     * Creates the input validator settings.
     *
     * @return CustomerInputValidatorSettings
     */
    protected function _getInputValidatorSettings()
    {
        $configurationProvider = $this->_getConfigurationProvider();
        
        $configurationSettings     = $this->_getValidatorConfigSettings($configurationProvider);
        $displaySettings           = $this->_getValidatorDisplaySettings($configurationProvider);
        $customerMinLengthSettings = $this->_getValidatorMinLengthSettings($configurationProvider);
        $maxLengthSettings         = $this->_getValidatorMaxLengthSettings();
        $errorMessages             = $this->_getValidatorErrorMessageSettings($configurationProvider);
        
        return MainFactory::create('CustomerInputValidatorSettings',
                                   $configurationSettings,
                                   $displaySettings,
                                   $customerMinLengthSettings,
                                   $maxLengthSettings,
                                   $errorMessages);
    }
    
    
    /**
     * Creates the configuration settings of the input validator.
     *
     * @param \CustomerConfigurationProvider $configProvider Component that fetches data from configuration tables.
     *
     * @return \CustomerConfigurationSettings
     */
    protected function _getValidatorConfigSettings(CustomerConfigurationProvider $configProvider)
    {
        $optionalNames          = $configProvider->configuration($this->_string('optionalNames'));
        $acceptPrivacy          = $configProvider->configuration($this->_string('acceptPrivacy'));
        $splitStreetInformation = $configProvider->configuration($this->_string('splitStreetInformation'));
        $genderMandatory        = $configProvider->configuration($this->_string('genderMandatory'));
        
        return MainFactory::create('CustomerConfigurationSettings',
                                   $this->_bool($optionalNames),
                                   $this->_bool($acceptPrivacy),
                                   $this->_bool($splitStreetInformation),
                                   $this->_bool($genderMandatory));
    }
    
    
    /**
     * Creates the display settings of the input validator.
     *
     * @param \CustomerConfigurationProvider $configProvider Component that fetches data from configuration tables.
     *
     * @return \CustomerDisplaySettings
     */
    protected function _getValidatorDisplaySettings(CustomerConfigurationProvider $configProvider)
    {
        $gender      = $this->_bool($configProvider->display($this->_string('gender')));
        $dob         = $this->_bool($configProvider->display($this->_string('dob')));
        $company     = $this->_bool($configProvider->display($this->_string('company')));
        $countryZone = $this->_bool($configProvider->display($this->_string('countryZone')));
        $telephone   = $this->_bool($configProvider->display($this->_string('telephone')));
        $fax         = $this->_bool($configProvider->display($this->_string('fax')));
        $suburb      = $this->_bool($configProvider->display($this->_string('suburb')));
        
        return MainFactory::create('CustomerDisplaySettings',
                                   $gender,
                                   $dob,
                                   $company,
                                   $countryZone,
                                   $telephone,
                                   $fax,
                                   $suburb);
    }
    
    
    /**
     * Creates the min length settings of the input validator.
     *
     * @param \CustomerConfigurationProvider $configProvider Component that fetches data from configuration tables.
     *
     * @return \CustomerMinLengthSettings
     */
    protected function _getValidatorMinLengthSettings(CustomerConfigurationProvider $configProvider)
    {
        $firstName       = $this->_int($configProvider->minLength($this->_string('firstName')));
        $lastName        = $this->_int($configProvider->minLength($this->_string('lastName')));
        $dob             = $this->_int($configProvider->minLength($this->_string('dob')));
        $email           = $this->_int($configProvider->minLength($this->_string('email')));
        $street          = $this->_int($configProvider->minLength($this->_string('street')));
        $houseNumber     = $this->_int($configProvider->minLength($this->_string('houseNumber')));
        $company         = $this->_int($configProvider->minLength($this->_string('company')));
        $postCode        = $this->_int($configProvider->minLength($this->_string('postcode')));
        $city            = $this->_int($configProvider->minLength($this->_string('city')));
        $countryZone     = $this->_int($configProvider->minLength($this->_string('countryZone')));
        $telephoneNumber = $this->_int($configProvider->minLength($this->_string('telephone')));
        $password        = $this->_int($configProvider->minLength($this->_string('password')));
        
        return MainFactory::create('CustomerMinLengthSettings',
                                   $firstName,
                                   $lastName,
                                   $dob,
                                   $email,
                                   $street,
                                   $houseNumber,
                                   $company,
                                   $postCode,
                                   $city,
                                   $countryZone,
                                   $telephoneNumber,
                                   $password);
    }
    
    
    /**
     * Creates the max length settings of the input validator.
     * The max length represent the database field max length.
     *
     * @return \CustomerMaxLengthSettings
     */
    protected function _getValidatorMaxLengthSettings()
    {
        // database fields max length
        $firstName   = $this->_int(64);
        $lastName    = $this->_int(64);
        $company     = $this->_int(255);
        $vatNumber   = $this->_int(20);
        $street      = $this->_int(64);
        $houseNumber = $this->_int(10);
        $postcode    = $this->_int(10);
        $city        = $this->_int(32);
        $countryZone = $this->_int(32);
        $suburb      = $this->_int(32);
        $telephone   = $this->_int(32);
        
        return MainFactory::create('CustomerMaxLengthSettings',
                                   $firstName,
                                   $lastName,
                                   $company,
                                   $vatNumber,
                                   $street,
                                   $houseNumber,
                                   $postcode,
                                   $city,
                                   $countryZone,
                                   $suburb,
                                   $telephone);
    }
    
    
    /**
     * Creates the customer validator error messages.
     *
     * @param \CustomerConfigurationProvider $configProvider Component that fetches data from configuration tables.
     *
     * @return \CustomerErrorMessageSettings
     */
    protected function _getValidatorErrorMessageSettings(CustomerConfigurationProvider $configProvider)
    {
        $gender               = $this->_string($configProvider->errorMessage($this->_string('gender')));
        $firstName            = $this->_string($configProvider->errorMessage($this->_string('firstName')));
        $lastName             = $this->_string($configProvider->errorMessage($this->_string('lastName')));
        $dob                  = $this->_string($configProvider->errorMessage($this->_string('dob')));
        $company              = $this->_string($configProvider->errorMessage($this->_string('company')));
        $vatNumber            = $this->_string($configProvider->errorMessage($this->_string('vatNumber')));
        $email                = $this->_string($configProvider->errorMessage($this->_string('email')));
        $emailAddressCheck    = $this->_string($configProvider->errorMessage($this->_string('emailAddressCheck')));
        $emailConfirmation    = $this->_string($configProvider->errorMessage($this->_string('emailConfirmation')));
        $emailExist           = $this->_string($configProvider->errorMessage($this->_string('emailExists')));
        $street               = $this->_string($configProvider->errorMessage($this->_string('street')));
        $houseNumber          = $this->_string($configProvider->errorMessage($this->_string('houseNumber')));
        $postcode             = $this->_string($configProvider->errorMessage($this->_string('postcode')));
        $city                 = $this->_string($configProvider->errorMessage($this->_string('city')));
        $country              = $this->_string($configProvider->errorMessage($this->_string('country')));
        $countryZone          = $this->_string($configProvider->errorMessage($this->_string('countryZone')));
        $countryZoneSelection = $this->_string($configProvider->errorMessage($this->_string('countryZoneSelection')));
        $telephone            = $this->_string($configProvider->errorMessage($this->_string('telephone')));
        $password             = $this->_string($configProvider->errorMessage($this->_string('password')));
        $passwordMismatch     = $this->_string($configProvider->errorMessage($this->_string('passwordMismatch')));
        $privacy              = $this->_string($configProvider->errorMessage($this->_string('privacy')));
        $invalidInput         = $this->_string($configProvider->errorMessage($this->_string('invalidInput')));
        
        return MainFactory::create('CustomerErrorMessageSettings',
                                   $gender,
                                   $firstName,
                                   $lastName,
                                   $dob,
                                   $company,
                                   $vatNumber,
                                   $email,
                                   $emailAddressCheck,
                                   $emailConfirmation,
                                   $emailExist,
                                   $street,
                                   $houseNumber,
                                   $postcode,
                                   $city,
                                   $country,
                                   $countryZone,
                                   $countryZoneSelection,
                                   $telephone,
                                   $password,
                                   $passwordMismatch,
                                   $privacy,
                                   $invalidInput);
    }
    
    
    /**
     * Returns a new int type.
     *
     * @param int $int Value of representing type object.
     *
     * @return \IntType
     */
    protected function _int($int)
    {
        return new \IntType($int);
    }
    
    
    /**
     * Returns a new string type.
     *
     * @param string $str Value of representing type object.
     *
     * @return \StringType
     */
    protected function _string($str)
    {
        return new \StringType($str);
    }
    
    
    /**
     * Returns a new bool type.
     *
     * @param bool $bool Value of representing type object.
     *
     * @return \BoolType
     */
    protected function _bool($bool)
    {
        return new \BoolType($bool);
    }
    
    
    /**
     * Creates a customer address deleter object.
     *
     * @return CustomerAddressDeleter Customer address deleter.
     */
    protected function _getCustomerAddressDeleter()
    {
        $deleter = MainFactory::create('CustomerAddressDeleter', $this->getDatabaseQueryBuilder());
        
        return $deleter;
    }
    
    
    /**
     * Creates a customer address reader object.
     *
     * @return CustomerAddressReader Customer address reader.
     */
    protected function _getCustomerAddressReader()
    {
        $customerFactory = $this->_getCustomerFactory();
        $countryService  = $this->getCountryService();
        
        $reader = MainFactory::create('CustomerAddressReader',
                                      $customerFactory,
                                      $countryService,
                                      $this->getDatabaseQueryBuilder());
        
        return $reader;
    }
    
    
    /**
     * Creates a customer country zone reader object.
     *
     * @return CustomerCountryZoneReader Customer country zone reader.
     */
    protected function _getCustomerCountryZoneReader()
    {
        $customerFactory = $this->_getCustomerFactory();
        $reader          = MainFactory::create('CustomerCountryZoneReader',
                                               $customerFactory,
                                               $this->getDatabaseQueryBuilder());
        
        return $reader;
    }
    
    
    /**
     * Creates a customer country reader object
     *
     * @return CustomerCountryReader Customer country reader.
     */
    protected function _getCustomerCountryReader()
    {
        $customerFactory = $this->_getCustomerFactory();
        $reader          = MainFactory::create('CustomerCountryReader',
                                               $customerFactory,
                                               $this->getDatabaseQueryBuilder());
        
        return $reader;
    }
    
    
    /**
     * Creates a customer writer object.
     *
     * @return CustomerWriter Customer writer.
     */
    protected function _getCustomerWriter()
    {
        $customerWriter = MainFactory::create('CustomerWriter', $this->getDatabaseQueryBuilder());
        
        return $customerWriter;
    }
    
    
    /**
     * Creates a customer reader object.
     *
     * @return CustomerReader Customer reader.
     */
    protected function _getCustomerReader()
    {
        $customerFactory           = $this->_getCustomerFactory();
        $customerAddressRepository = $this->_getCustomerAddressRepository();
        $dbQueryBuilder            = $this->getDatabaseQueryBuilder();
        
        $customerReader = MainFactory::create('CustomerReader',
                                              $customerFactory,
                                              $customerAddressRepository,
                                              $dbQueryBuilder);
        
        return $customerReader;
    }
    
    
    /**
     * Creates a customer deleter object.
     *
     * @return CustomerDeleter Customer deleter.
     */
    protected function _getCustomerDeleter()
    {
        $customerDeleter = MainFactory::create('CustomerDeleter', $this->getDatabaseQueryBuilder());
        
        return $customerDeleter;
    }
    
    
    /**
     * Creates a customer service settings object.
     *
     * @return CustomerServiceSettings Customer service settings.
     */
    protected function _getCustomerServiceSettings()
    {
        $configProvider = $this->_getConfigurationProvider();
        
        $defaultCustomerStatusId = $configProvider->defaultStatusId($this->_string('customer'));
        $defaultGuestStatusId    = $configProvider->defaultStatusId($this->_string('guest'));
        $moveOnlyIfNoGuest       = $configProvider->configuration($this->_string('moveOnlyIfNoGuest'));
        
        return MainFactory::create('CustomerServiceSettings',
                                   new IdType($defaultCustomerStatusId),
                                   new IdType($defaultGuestStatusId),
                                   $this->_bool($moveOnlyIfNoGuest));
    }
    
    
    /**
     * Creates a customer address writer object.
     *
     * @return CustomerAddressWriter Customer address writer.
     */
    protected function _getCustomerAddressWriter()
    {
        $writer = MainFactory::create('CustomerAddressWriter', $this->getDatabaseQueryBuilder());
        
        return $writer;
    }
    
    
    protected function _getAddonValueService()
    {
        $addonValueStorageFactory = MainFactory::create('AddonValueStorageFactory', $this->getDatabaseQueryBuilder());
        $addonValueService        = MainFactory::create('AddonValueService', $addonValueStorageFactory);
        
        return $addonValueService;
    }
    
    
    protected function _getConfigurationProvider()
    {
        if (null === $this->configurationProvider) {
            $this->configurationProvider = new CustomerConfigurationProvider($this->getDatabaseQueryBuilder(),
                                                                             MainFactory::create('LanguageTextManager'));
        }
        
        return $this->configurationProvider;
    }
} 