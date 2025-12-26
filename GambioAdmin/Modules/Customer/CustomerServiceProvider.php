<?php
/*--------------------------------------------------------------
   CustomerServiceProvider.php 2023-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Layout\Menu\AdminMenuService;
use Gambio\Admin\Modules\Country\Services\CountryReadService as CountryReadServiceInterface;
use Gambio\Admin\Modules\Currency\Services\CurrencyFilterService as CurrencyFilterServiceInterface;
use Gambio\Admin\Modules\Customer\App\Actions\Json\ChangeCustomerGroupAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\ChangePasswordAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\CreateCustomerAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\CustomerConfigAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\CustomerProfileAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\CustomersOverviewAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\DeleteCustomerAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\DeleteOutdatedGuestAccountsAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\FetchLogAdminActivitiesStatusAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\FetchProductNameAndImageAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\NextCustomerNumberAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\PatchCustomerAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\PatchUserConfigurationAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\RegistrationAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\SearchCustomerAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\SetDisallowedPaymentAndShippingMethodsAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\SetLogAdminActivitiesStatusAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\UpdateCustomerAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\ValidateEmailAddressAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\ValidateVatAction;
use Gambio\Admin\Modules\Customer\App\Actions\Vue\OverviewAction;
use Gambio\Admin\Modules\Customer\App\Actions\Vue\ProfileAction;
use Gambio\Admin\Modules\Customer\App\AdminAccessUserRepository;
use Gambio\Admin\Modules\Customer\App\CustomerAdditionalInformationRepository;
use Gambio\Admin\Modules\Customer\App\CustomerAdministratorPermissionRepository;
use Gambio\Admin\Modules\Customer\App\CustomerConfigurationProvider;
use Gambio\Admin\Modules\Customer\App\CustomerPasswordWriteService;
use Gambio\Admin\Modules\Customer\App\CustomerProductRepository;
use Gambio\Admin\Modules\Customer\App\CustomerSearchService;
use Gambio\Admin\Modules\Customer\App\CustomerUserConfigurationRepository;
use Gambio\Admin\Modules\Customer\App\Data\CustomerMapper;
use Gambio\Admin\Modules\Customer\App\Data\CustomerReader;
use Gambio\Admin\Modules\Customer\App\Data\CustomerWriter;
use Gambio\Admin\Modules\Customer\App\Data\LegacyCustomerGroupReader;
use Gambio\Admin\Modules\Customer\App\LogAdminActivityRepository;
use Gambio\Admin\Modules\Customer\App\NextCustomerNumberRepository;
use Gambio\Admin\Modules\Customer\App\RegistrationRequestParser;
use Gambio\Admin\Modules\Customer\App\RegistrationRequestValidator;
use Gambio\Admin\Modules\Customer\Services\CustomerFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerFilterFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerFilterService;
use Gambio\Admin\Modules\Customer\Services\CustomerPasswordWriteService as CustomerPasswordWriteServiceInterface;
use Gambio\Admin\Modules\Customer\Services\CustomerReadService;
use Gambio\Admin\Modules\Customer\Services\CustomerRepository;
use Gambio\Admin\Modules\Customer\Services\CustomerSearchService as CustomerSearchServiceInterface;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressFactory;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressReadService as CustomerDefaultAddressReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressWriteService;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryReadService as CustomerHistoryReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFilterService as CustomerMemoFilterServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\CustomerStatisticsReadService as CustomerStatisticsReadServiceInterface;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterReadService as CustomerNewsletterReadServiceInterface;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterWriteService;
use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsReadService as CustomerDisallowedPaymentMethodsReadServiceInterface;
use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsWriteService as CustomerDisallowedPaymentMethodsWriteServiceInterface;
use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsReadService as CustomerDisallowedShippingMethodsReadServiceInterface;
use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsWriteService as CustomerDisallowedShippingMethodsWriteServiceInterface;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Core\UserConfiguration\Services\UserConfigurationService;
use Gambio\Core\VatValidation\Services\VatValidationService as VatValidationServiceInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class CustomerServiceProvider
 *
 * @package Gambio\Admin\Modules\Customer
 * @codeCoverageIgnore
 */
class CustomerServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CustomerFactory::class,
            CustomerFilterFactory::class,
            CustomerFilterService::class,
            CustomerReadService::class,
            CustomerRepository::class,
            CustomerWriteService::class,
            CustomerPasswordWriteServiceInterface::class,
            OverviewAction::class,
            ProfileAction::class,
            CreateCustomerAction::class,
            DeleteOutdatedGuestAccountsAction::class,
            DeleteCustomerAction::class,
            CustomersOverviewAction::class,
            CustomerProfileAction::class,
            UpdateCustomerAction::class,
            PatchCustomerAction::class,
            CustomerConfigAction::class,
            ChangePasswordAction::class,
            ValidateVatAction::class,
            FetchProductNameAndImageAction::class,
            CustomerProductRepository::class,
            SetLogAdminActivitiesStatusAction::class,
            FetchLogAdminActivitiesStatusAction::class,
            SetDisallowedPaymentAndShippingMethodsAction::class,
            ChangeCustomerGroupAction::class,
            ValidateEmailAddressAction::class,
            RegistrationAction::class,
            PatchUserConfigurationAction::class,
            CustomerSearchServiceInterface::class,
            SearchCustomerAction::class,
            NextCustomerNumberAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CustomerFactory::class);
        $this->application->registerShared(CustomerMapper::class);
        $this->application->registerShared(CustomerFilterFactory::class);
        
        $this->application->registerShared(LegacyCustomerGroupReader::class)
            ->addArgument(Connection::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(CustomerWriter::class)
            ->addArgument(Connection::class)
            ->addArgument(VatValidationServiceInterface::class);
        
        $this->application->registerShared(CustomerReader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(CustomerRepository::class, App\CustomerRepository::class)
            ->addArgument(CustomerMapper::class)
            ->addArgument(CustomerReader::class)
            ->addArgument(CustomerWriter::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(CustomerFilterService::class, App\CustomerFilterService::class)
            ->addArgument(CustomerRepository::class)
            ->addArgument(CustomerFilterFactory::class);
        
        $this->application->registerShared(CustomerSearchServiceInterface::class, CustomerSearchService::class)
            ->addArgument(CustomerFilterFactory::class)
            ->addArgument(CustomerRepository::class);
        
        $this->application->registerShared(CustomerReadService::class, App\CustomerReadService::class)
            ->addArgument(CustomerFactory::class)
            ->addArgument(CustomerRepository::class);
        
        $this->application->registerShared(CustomerWriteService::class, App\CustomerWriteService::class)
            ->addArgument(CustomerFactory::class)
            ->addArgument(CustomerRepository::class)
            ->addArgument(ConfigurationService::class);
        
        $this->application->registerShared(CustomerPasswordWriteServiceInterface::class,
                                           CustomerPasswordWriteService::class)
            ->addArgument(ConfigurationFinder::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(OverviewAction::class)
            ->addArgument(CustomerUserConfigurationRepository::class);
        
        $this->application->registerShared(ProfileAction::class)
            ->addArgument(AdminMenuService::class);
        
        $this->application->registerShared(CustomerFilterService::class)
            ->addArgument(CustomerRepository::class)
            ->addArgument(CustomerFilterFactory::class);
        
        $this->application->registerShared(CustomersOverviewAction::class)
            ->addArgument(CustomerFilterFactory::class)
            ->addArgument(CustomerRepository::class)
            ->addArgument(LegacyCustomerGroupReader::class)
            ->addArgument(CustomerAdministratorPermissionRepository::class)
            ->addArgument(CustomerConfigurationProvider::class)
            ->addArgument(CountryReadServiceInterface::class)
            ->addArgument(CurrencyFilterServiceInterface::class)
            ->addArgument(CustomerUserConfigurationRepository::class)
            ->addArgument(CustomerAdditionalInformationRepository::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(CustomerUserConfigurationRepository::class)
            ->addArgument(UserConfigurationService::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(CustomerProfileAction::class)
            ->addArgument(CustomerReadService::class)
            ->addArgument(CustomerMemoFilterServiceInterface::class)
            ->addArgument(LegacyCustomerGroupReader::class)
            ->addArgument(CustomerStatisticsReadServiceInterface::class)
            ->addArgument(CurrencyFilterServiceInterface::class)
            ->addArgument(CustomerHistoryReadServiceInterface::class)
            ->addArgument(CustomerConfigurationProvider::class)
            ->addArgument(CustomerDisallowedPaymentMethodsReadServiceInterface::class)
            ->addArgument(CustomerDisallowedShippingMethodsReadServiceInterface::class)
            ->addArgument(CustomerDefaultAddressReadServiceInterface::class)
            ->addArgument(CustomerProductRepository::class)
            ->addArgument(CountryReadServiceInterface::class)
            ->addArgument(LogAdminActivityRepository::class)
            ->addArgument(CustomerUserConfigurationRepository::class)
            ->addArgument(CustomerAdministratorPermissionRepository::class)
            ->addArgument(CustomerNewsletterReadServiceInterface::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(DeleteCustomerAction::class)->addArgument(CustomerWriteService::class);
        
        $this->application->registerShared(DeleteOutdatedGuestAccountsAction::class)
            ->addArgument(CustomerWriteService::class);
        
        $this->application->registerShared(CreateCustomerAction::class)
            ->addArgument(CustomerWriteService::class)
            ->addArgument(CustomerFactory::class);
        
        $this->application->registerShared(UpdateCustomerAction::class)
            ->addArgument(CustomerReadService::class)
            ->addArgument(CustomerWriteService::class)
            ->addArgument(CustomerFactory::class);
        
        $this->application->registerShared(PatchCustomerAction::class)
            ->addArgument(CustomerReadService::class)
            ->addArgument(CustomerWriteService::class)
            ->addArgument(CustomerFactory::class);
        
        $configProvider = $this->application->registerShared(CustomerConfigurationProvider::class)
            ->addArgument(ConfigurationService::class);
        
        array_map([$configProvider, 'addArgument'], $this->customerConfigurationKeys());
        
        $this->application->registerShared(CustomerConfigAction::class)->addArgument(CustomerConfigurationProvider::class);
        $this->application->registerShared(ChangePasswordAction::class)->addArgument(CustomerPasswordWriteServiceInterface::class);
        $this->application->registerShared(ValidateVatAction::class)->addArgument(VatValidationServiceInterface::class);
        
        $this->application->registerShared(CustomerProductRepository::class)->addArgument(Connection::class);
        $this->application->registerShared(FetchProductNameAndImageAction::class)
            ->addArgument(CustomerProductRepository::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(LogAdminActivityRepository::class)->addArgument(Connection::class);
        $this->application->registerShared(SetLogAdminActivitiesStatusAction::class)->addArgument(LogAdminActivityRepository::class);
        $this->application->registerShared(FetchLogAdminActivitiesStatusAction::class)->addArgument(LogAdminActivityRepository::class);
        
        $this->application->registerShared(SetDisallowedPaymentAndShippingMethodsAction::class)
            ->addArgument(CustomerDisallowedPaymentMethodsWriteServiceInterface::class)
            ->addArgument(CustomerDisallowedShippingMethodsWriteServiceInterface::class);
        
        $this->application->registerShared(ChangeCustomerGroupAction::class)
            ->addArgument(CustomerReadService::class)
            ->addArgument(CustomerWriteService::class)
            ->addArgument(CustomerFactory::class)
            ->addArgument(AdminAccessUserRepository::class);
        
        $this->application->registerShared(CustomerAdministratorPermissionRepository::class)->addArgument(Connection::class);
        
        $this->application->registerShared(ValidateEmailAddressAction::class)
            ->addArgument(CustomerReadService::class)
            ->addArgument(TextManager::class);
        $this->application->registerShared(CustomerAdditionalInformationRepository::class)->addArgument(Connection::class);
    
        $this->application->registerShared(RegistrationRequestParser::class)
            ->addArgument(CustomerFactory::class)
            ->addArgument(CustomerAddressFactory::class);
    
        $this->application->registerShared(RegistrationRequestValidator::class);
        
        $this->application->registerShared(RegistrationAction::class)
            ->addArgument(CustomerWriteService::class)
            ->addArgument(CustomerDefaultAddressWriteService::class)
            ->addArgument(CustomerPasswordWriteServiceInterface::class)
            ->addArgument(RegistrationRequestValidator::class)
            ->addArgument(RegistrationRequestParser::class)
            ->addArgument(CustomerNewsletterWriteService::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(PatchUserConfigurationAction::class)->addArgument(CustomerUserConfigurationRepository::class);
        
        $this->application->registerShared(SearchCustomerAction::class)
            ->addArgument(CustomerSearchServiceInterface::class)
            ->addArgument(CustomerAdditionalInformationRepository::class);
        
        $this->application->registerShared(NextCustomerNumberRepository::class)->addArgument(Connection::class);
        $this->application->registerShared(NextCustomerNumberAction::class)->addArgument(NextCustomerNumberRepository::class);
        
        $this->application->registerShared(AdminAccessUserRepository::class)->addArgument(Connection::class);
    }
    
    
    /**
     * @return array
     */
    private function customerConfigurationKeys(): array
    {
        return [
            // Personal Fields
            'configuration/ACCOUNT_DOB',
            'configuration/ACCOUNT_GENDER',
            'configuration/GENDER_MANDATORY',
            'configuration/ACCOUNT_NAMES_OPTIONAL',
            
            // Contact Fields
            'configuration/ACCOUNT_FAX',
            'configuration/ACCOUNT_TELEPHONE',

            // Business Fields
            'configuration/ACCOUNT_B2B_STATUS',
            'configuration/ACCOUNT_COMPANY',
            'configuration/ENTRY_COMPANY_MIN_LENGTH',
            
            // VAT ID @todo
            'configuration/ACCOUNT_VAT_BLOCK_ERROR',
            'configuration/ACCOUNT_COMPANY_VAT_CHECK',
            'configuration/ACCOUNT_COMPANY_VAT_LIVE_CHECK',

            // Address Fields
            'configuration/ACCOUNT_ADDITIONAL_INFO',
            'configuration/ACCOUNT_SPLIT_STREET_INFORMATION',
            'configuration/ACCOUNT_STATE',
            'configuration/ACCOUNT_SUBURB',
            
            // Configuration Fields
            'configuration/DEFAULT_CUSTOMERS_STATUS_ID',
            'configuration/DEFAULT_CUSTOMERS_STATUS_ID_GUEST',
            'configuration/STORE_NAME',
            'configuration/MODULE_PAYMENT_INSTALLED',
            'configuration/STORE_COUNTRY',
            'configuration/STORE_ZONE',
            'gm_configuration/DATA_TRANSFER_TO_TRANSPORT_COMPANIES_SETTINGS',
            'gm_configuration/MANUAL_ORDER_PAYMENT',
        ];
    }
}