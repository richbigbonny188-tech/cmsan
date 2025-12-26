<?php
/*--------------------------------------------------------------
   CustomerProfileAction.php 2022-11-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use DateTimeInterface;
use Exception;
use Gambio\Admin\Modules\Country\Services\CountryReadService as CountryReadServiceInterface;
use Gambio\Admin\Modules\Currency\Services\CurrencyFilterService as CurrencyFilterServiceInterface;
use Gambio\Admin\Modules\Customer\App\CustomerAdministratorPermissionRepository;
use Gambio\Admin\Modules\Customer\App\CustomerConfigurationProvider;
use Gambio\Admin\Modules\Customer\App\CustomerProductRepository;
use Gambio\Admin\Modules\Customer\App\CustomerUserConfigurationRepository;
use Gambio\Admin\Modules\Customer\App\Data\LegacyCustomerGroupReader;
use Gambio\Admin\Modules\Customer\App\LogAdminActivityRepository;
use Gambio\Admin\Modules\Customer\Model\Customer;
use Gambio\Admin\Modules\Customer\Services\CustomerReadService;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerDoesNotExistException;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressReadService as CustomerDefaultAddressReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\History\Services\CustomerHistoryReadService as CustomerHistoryReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFilterService;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\CustomerStatisticsReadService as CustomerStatisticsReadServiceInterface;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterReadService as CustomerNewsletterReadServiceInterface;
use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsReadService as CustomerDisallowedPaymentMethodsReadServiceInterface;
use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsReadService as CustomerDisallowedShippingMethodsReadServiceInterface;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use function Gambio\Core\Application\env;

/**
 * Class CustomerProfileAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class CustomerProfileAction
{
    private CustomerReadService                                   $customerReadService;
    private CustomerMemoFilterService                             $customerMemoReadService;
    private LegacyCustomerGroupReader                             $legacyCustomerGroupReader;
    private CustomerStatisticsReadServiceInterface                $customerStatisticsReadService;
    private CurrencyFilterServiceInterface                        $currencyFilterService;
    private CustomerHistoryReadServiceInterface                   $historyReadService;
    private CustomerConfigurationProvider                         $configProvider;
    private CustomerDisallowedPaymentMethodsReadServiceInterface  $customerDisallowedPaymentMethodsReadService;
    private CustomerDisallowedShippingMethodsReadServiceInterface $customerDisallowedShippingMethodsReadService;
    private CustomerDefaultAddressReadServiceInterface            $customerDefaultAddressReadService;
    private CustomerProductRepository                             $customerProductRepository;
    private CountryReadServiceInterface                           $countryReadService;
    private LogAdminActivityRepository                            $adminActivityRepository;
    private CustomerUserConfigurationRepository                   $userConfigurationRepository;
    private CustomerAdministratorPermissionRepository             $administratorPermissionRepository;
    private string                                                $datetimeFormat;
    private int                                                   $languageId;
    private array                                                 $customers = [];
    private ?int                                                  $userId;
    private CustomerNewsletterReadServiceInterface                $customerNewsletterReadService;
    
    
    /**
     * @param CustomerReadService                                   $customerReadService
     * @param CustomerMemoFilterService                             $customerMemoReadService
     * @param LegacyCustomerGroupReader                             $legacyCustomerGroupReader
     * @param CustomerStatisticsReadServiceInterface                $customerStatisticsReadService
     * @param CurrencyFilterServiceInterface                        $currencyFilterService
     * @param CustomerHistoryReadServiceInterface                   $historyReadService
     * @param CustomerConfigurationProvider                         $configProvider
     * @param CustomerDisallowedPaymentMethodsReadServiceInterface  $customerDisallowedPaymentMethodsReadService
     * @param CustomerDisallowedShippingMethodsReadServiceInterface $customerDisallowedShippingMethodsReadService
     * @param CustomerDefaultAddressReadServiceInterface            $customerDefaultAddressReadService
     * @param CustomerProductRepository                             $customerProductRepository
     * @param CountryReadServiceInterface                           $countryReadService
     * @param LogAdminActivityRepository                            $adminActivityRepository
     * @param CustomerUserConfigurationRepository                   $userConfigurationRepository
     * @param CustomerAdministratorPermissionRepository             $administratorPermissionRepository
     * @param CustomerNewsletterReadServiceInterface                $customerNewsletterReadService
     * @param UserPreferences                                       $userPreferences
     */
    public function __construct(
        CustomerReadService                                   $customerReadService,
        CustomerMemoFilterService                             $customerMemoReadService,
        LegacyCustomerGroupReader                             $legacyCustomerGroupReader,
        CustomerStatisticsReadServiceInterface                $customerStatisticsReadService,
        CurrencyFilterServiceInterface                        $currencyFilterService,
        CustomerHistoryReadServiceInterface                   $historyReadService,
        CustomerConfigurationProvider                         $configProvider,
        CustomerDisallowedPaymentMethodsReadServiceInterface  $customerDisallowedPaymentMethodsReadService,
        CustomerDisallowedShippingMethodsReadServiceInterface $customerDisallowedShippingMethodsReadService,
        CustomerDefaultAddressReadServiceInterface            $customerDefaultAddressReadService,
        CustomerProductRepository                             $customerProductRepository,
        CountryReadServiceInterface                           $countryReadService,
        LogAdminActivityRepository                            $adminActivityRepository,
        CustomerUserConfigurationRepository                   $userConfigurationRepository,
        CustomerAdministratorPermissionRepository             $administratorPermissionRepository,
        CustomerNewsletterReadServiceInterface                $customerNewsletterReadService,
        UserPreferences                                       $userPreferences
    ) {
        $this->customerReadService                          = $customerReadService;
        $this->customerMemoReadService                      = $customerMemoReadService;
        $this->legacyCustomerGroupReader                    = $legacyCustomerGroupReader;
        $this->customerStatisticsReadService                = $customerStatisticsReadService;
        $this->currencyFilterService                        = $currencyFilterService;
        $this->historyReadService                           = $historyReadService;
        $this->configProvider                               = $configProvider;
        $this->customerDisallowedPaymentMethodsReadService  = $customerDisallowedPaymentMethodsReadService;
        $this->customerDisallowedShippingMethodsReadService = $customerDisallowedShippingMethodsReadService;
        $this->customerDefaultAddressReadService            = $customerDefaultAddressReadService;
        $this->customerProductRepository                    = $customerProductRepository;
        $this->countryReadService                           = $countryReadService;
        $this->adminActivityRepository                      = $adminActivityRepository;
        $this->userConfigurationRepository                  = $userConfigurationRepository;
        $this->administratorPermissionRepository            = $administratorPermissionRepository;
        $this->customerNewsletterReadService                = $customerNewsletterReadService;
        $this->datetimeFormat                               = DateTimeInterface::ATOM;
        $this->languageId                                   = $userPreferences->languageId();
        $this->userId                                       = $userPreferences->userId();
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $body       = $request->getParsedBody();
            $customerId = (int)$body['customerId'];
            
            if ($customerId <= 0) {
                return $response->withStatus(400)->withJson([
                                                                'errors' => 'Invalid customer ID given. Got: '
                                                                            . $body['customerId'],
                                                            ]);
            }
    
            $customer                  = $this->customerReadService->getCustomerById($customerId);
            $customerGroups            = $this->legacyCustomerGroupReader->getCustomerGroups();
            $customerMemos             = $this->customerMemoReadService->filterCustomerMemos($customerId,
                                                                                             [],
                                                                                             '-creationTime');
            $customerMemos             = $this->addCustomerObjectToMemos($customerMemos->toArray($this->datetimeFormat));
            $statistics                = $this->customerStatisticsReadService->getCustomerStatistics($customerId)
                ->toArray();
            $defaultCurrency           = $this->currencyFilterService->filterCurrencies(['isDefault' => 'true'])
                                             ->toArray()[0];
            $history                   = $this->historyReadService->getCustomerHistory($customerId)
                ->toArray($this->datetimeFormat);
            $history                   = $this->addCustomerObjectToHistoryEntries($history);
            $configurations            = $this->configProvider->getConfigurations();
            $disallowedPaymentMethods  = $this->customerDisallowedPaymentMethodsReadService->getCustomersDisallowedPaymentMethods($customerId);
            $disallowedShippingMethods = $this->customerDisallowedShippingMethodsReadService->getCustomersDisallowedShippingMethods($customerId);
            $logAdminActivities        = $this->adminActivityRepository->getLogAdminActivitiesStatus($customerId);
            $activeCountries           = $this->parseActiveCountries($this->countryReadService->getActiveCountries($this->languageId)->toArray());
    
            $data = [
                'userId'                       => $this->userId,
                'customer'                     => $customer->toArray($this->datetimeFormat),
                'customerMemos'                => $customerMemos,
                'customerGroups'               => $customerGroups,
                'currency'                     => $defaultCurrency,
                'statistics'                   => $statistics,
                'history'                      => $history,
                'configurations'               => $configurations,
                'disallowedPaymentMethods'     => $disallowedPaymentMethods->toArray(),
                'disallowedShippingMethods'    => $disallowedShippingMethods->toArray(),
                'activeCountries'              => $activeCountries,
                'address'                      => $this->customerDefaultAddressReadService->getDefaultShippingAddress($customerId)
                    ->toArray($this->datetimeFormat),
                'logAdminActivities'           => $logAdminActivities,
                'securityToken'                => env('APP_SECURITY_TOKEN'),
                'newsletterSubscriptionStatus' => $this->customerNewsletterReadService->isCustomerSubscribed($customerId),
                'userConfigurations'           => [
                    'SHOW_WARNING_ON_LOGIN_AS_CUSTOMER' => $this->userConfigurationRepository->getShowWarningOnLoginAsCustomerValue(),
                ],
                'isSuperAdministrator'         => $this->administratorPermissionRepository->getPermissions($customerId)['super'],
                'adminPermissions'             => $this->administratorPermissionRepository->getPermissions($this->userId),
            ];
    
            $this->addProductInformationToDataArray($data);
            
            return $response->withJson($data);
        } catch (Exception $exception) {
            return $response->withStatus(404)->withJson(['errors' => $exception->getMessage()]);
        }
    }
    
    
    /**
     * @param array $memos
     *
     * @return array
     * @throws CustomerDoesNotExistException
     */
    private function addCustomerObjectToMemos(array $memos): array
    {
        foreach ($memos as &$memo) {
            try {
                $memo['creator'] = $this->getCustomerById($memo['creatorId'])->toArray($this->datetimeFormat);
            }
            catch (CustomerDoesNotExistException $exception)
            {
                $memo['creator'] = $this->getCustomerById(1)->toArray($this->datetimeFormat);
            }
        }
        
        return $memos;
    }
    
    
    /**
     * @param array $history
     *
     * @return array
     */
    private function addCustomerObjectToHistoryEntries(array $history): array
    {
        foreach ($history as &$entry) {
            // appending the admin customer object if the customer was signed up to the newsletter by an admin
            if ($entry['type'] === 'newsletter' && $entry['payload']['createdByAdmin']) {
    
                try {
                    $entry['payload']['admin'] = $this->getCustomerById($entry['payload']['adminId'])->toArray($this->datetimeFormat);
                } catch (CustomerDoesNotExistException $e) {
                    // the admin was deleted after signing the user up for the newsletter
                    $entry['payload']['admin'] = null;
                }
            }
        }
        
        return $history;
    }
    
    
    /**
     * @param int $customerId
     *
     * @return Customer
     * @throws CustomerDoesNotExistException
     */
    private function getCustomerById(int $customerId): Customer
    {
        return $this->customers[$customerId] ??
               $this->customers[$customerId] = $this->customerReadService->getCustomerById($customerId);
    }
    
    
    /**
     * @param array $data
     *
     * @return void
     */
    private function addProductInformationToDataArray(array &$data): void
    {
        $productIds = [];
        
        foreach ($data['history'] as $entry) {
            
            if (isset($entry['payload']['products_id'])) {
                
                $productIds[] = (int)$entry['payload']['products_id'];
            }
        }
        
        $data['products'] = $this->customerProductRepository->getProductsNameAndImage($this->languageId, ...$productIds)
            ->toArray();
    }
    
    
    /**
     * Parses the country zones to return only the "id" and "name", which are used by the front-end
     *
     * @param array $activeCountries
     *
     * @return array
     */
    private function parseActiveCountries(array $activeCountries): array
    {
        $parsedCountries = [];
    
        foreach ($activeCountries as $activeCountry) {
            $parsedZones = array_map(static function($zone) {
                return ['id' => $zone['id'], 'name' => $zone['name']];
            }, $activeCountry['zones']);
            
            $parsedCountries[] = array_merge($activeCountry, ['zones' => $parsedZones]);
        }
        
        return $parsedCountries;
    }
}