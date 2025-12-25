<?php
/*--------------------------------------------------------------
   CustomersOverviewAction.php 2022-11-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use DateTimeInterface;
use Gambio\Admin\Modules\Country\Services\CountryReadService as CountryReadServiceInterface;
use Gambio\Admin\Modules\Currency\Services\CurrencyFilterService as CurrencyFilterServiceInterface;
use Gambio\Admin\Modules\Customer\App\CustomerAdditionalInformationRepository;
use Gambio\Admin\Modules\Customer\App\CustomerAdministratorPermissionRepository;
use Gambio\Admin\Modules\Customer\App\CustomerConfigurationProvider;
use Gambio\Admin\Modules\Customer\App\CustomerUserConfigurationRepository;
use Gambio\Admin\Modules\Customer\App\Data\LegacyCustomerGroupReader;
use Gambio\Admin\Modules\Customer\Model\Collections\Customers;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerFilters;
use Gambio\Admin\Modules\Customer\Services\CustomerFilterFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerRepository;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Application\Responses\ResponseDataTrimmerTrait;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use function Gambio\Core\Application\env;

/**
 * Class CustomersOverviewAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class CustomersOverviewAction
{
    use CreateApiMetaDataTrait;
    use ResponseDataTrimmerTrait;
    
    private CustomerFilterFactory                     $filterFactory;
    private CustomerRepository                        $repository;
    private LegacyCustomerGroupReader                 $legacyCustomerGroupReader;
    private string                                    $datetimeFormat;
    private CustomerAdministratorPermissionRepository $administratorPermissionRepository;
    private CustomerConfigurationProvider             $configProvider;
    private CountryReadServiceInterface               $countryReadService;
    private int                                       $languageId;
    private ?int                                      $userId;
    private CurrencyFilterServiceInterface            $currencyFilterService;
    private CustomerUserConfigurationRepository       $userConfigurationRepository;
    private CustomerAdditionalInformationRepository   $additionalInformationRepository;
    
    
    /**
     * @param CustomerFilterFactory                     $filterFactory
     * @param CustomerRepository                        $repository
     * @param LegacyCustomerGroupReader                 $legacyCustomerGroupReader
     * @param CustomerAdministratorPermissionRepository $administratorPermissionRepository
     * @param CustomerConfigurationProvider             $configProvider
     * @param CountryReadServiceInterface               $countryReadService
     * @param CurrencyFilterServiceInterface            $currencyFilterService
     * @param CustomerUserConfigurationRepository       $userConfigurationRepository
     * @param CustomerAdditionalInformationRepository   $additionalInformationRepository
     * @param UserPreferences                           $userPreferences
     */
    public function __construct(
        CustomerFilterFactory                     $filterFactory,
        CustomerRepository                        $repository,
        LegacyCustomerGroupReader                 $legacyCustomerGroupReader,
        CustomerAdministratorPermissionRepository $administratorPermissionRepository,
        CustomerConfigurationProvider             $configProvider,
        CountryReadServiceInterface               $countryReadService,
        CurrencyFilterServiceInterface            $currencyFilterService,
        CustomerUserConfigurationRepository       $userConfigurationRepository,
        CustomerAdditionalInformationRepository   $additionalInformationRepository,
        UserPreferences                           $userPreferences
    ) {
        $this->filterFactory                     = $filterFactory;
        $this->repository                        = $repository;
        $this->legacyCustomerGroupReader         = $legacyCustomerGroupReader;
        $this->datetimeFormat                    = DateTimeInterface::ATOM;
        $this->administratorPermissionRepository = $administratorPermissionRepository;
        $this->configProvider                    = $configProvider;
        $this->countryReadService                = $countryReadService;
        $this->currencyFilterService             = $currencyFilterService;
        $this->userConfigurationRepository       = $userConfigurationRepository;
        $this->additionalInformationRepository   = $additionalInformationRepository;
        $this->languageId                        = $userPreferences->languageId();
        $this->userId                            = $userPreferences->userId();
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
        $sortBy  = $request->getQueryParam('sort', 'id');
        $page    = (int)$request->getQueryParam('page', 1);
        $perPage = (int)$request->getQueryParam('per-page', 25);
        $offset  = $perPage * ($page - 1);
        
        $filters    = $this->getFilters($request);
        $sorting    = $this->filterFactory->createSorting($sortBy);
        $pagination = $this->filterFactory->createPagination($perPage, $offset);
        
        $customers      = $this->repository->filterCustomers($filters, $sorting, $pagination);
        $customerGroups = $this->legacyCustomerGroupReader->getCustomerGroups();
        $totalItems     = $this->repository->getCustomersTotalCount($filters);
        
        $configurations  = $this->configProvider->getConfigurations();
        $defaultCurrency = $this->currencyFilterService->filterCurrencies(['isDefault' => 'true'])->toArray()[0];
        
        $metaData   = $this->createApiCollectionMetaData($page,
                                                         $perPage,
                                                         $totalItems,
                                                         $this->getResourceUrlFromRequest($request),
                                                         $request->getQueryParams());
    
        $storeCountry = $this->getStoreCountryById($configurations['STORE_COUNTRY'] ?? 0);
        $mergedConfigs = $configurations + ['storeCountry' => $storeCountry];
        
        return $response->withJson([
                                       'userId'                => $this->userId,
                                       'customers'             => $customers->toArray($this->datetimeFormat),
                                       'isSuperAdministrator'  => $this->getSuperAdminStatus($customers),
                                       'customerGroups'        => $customerGroups,
                                       'securityToken'         => env('APP_SECURITY_TOKEN'),
                                       'configurations'        => $mergedConfigs,
                                       'activeCountries'       => $this->countryReadService->getActiveCountries($this->languageId)
                                           ->toArray(),
                                       'adminPermissions'      => $this->administratorPermissionRepository->getPermissions($this->userId),
                                       'currency'              => $defaultCurrency,
                                       'additionalInformation' => $this->additionalInformationRepository->getAdditionalInformation($this->datetimeFormat,
                                           ...
                                                                                                                                   array_column($customers->toArray(),
                                                                                                                                                'id')),
                                       'userConfigurations'    => [
                                           'SHOW_WARNING_ON_LOGIN_AS_CUSTOMER' => $this->userConfigurationRepository->getShowWarningOnLoginAsCustomerValue(),
                                       ],
                                       '_meta'                 => $metaData,
                                   ]);
    }
    
    
    /**
     * @param Request $request
     *
     * @return CustomerFilters
     */
    private function getFilters(Request $request): CustomerFilters
    {
        return $this->filterFactory->createFilters($request->getQueryParam('filter', []));
    }
    
    
    /**
     * @param Customers $customers
     *
     * @return array
     */
    private function getSuperAdminStatus(Customers $customers): array
    {
        $repo         = $this->administratorPermissionRepository;
        $customersIds = array_column($customers->toArray(), 'id');
        $callback     = fn(int $id): array => ['id' => $id, 'status' => $repo->getPermissions($id)['super'],];
        
        return array_map($callback, $customersIds);
    }
    
    /**
     * @param Request $request
     *
     * @return string
     */
    private function getResourceUrlFromRequest(Request $request): string
    {
        return $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getPath();
    }
    
    
    /**
     * @param int $countryId
     *
     * @return array
     */
    private function getStoreCountryById(int $countryId): array
    {
        try {
            $country = $this->countryReadService->getCountryById($countryId, $this->languageId);
            
            return ['name' => $country->name(), 'isoCode2' => $country->isoCode2()];
        } catch (\Throwable $e) {
            return [];
        }
    }
}