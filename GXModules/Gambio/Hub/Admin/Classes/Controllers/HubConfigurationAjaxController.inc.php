<?php
/* --------------------------------------------------------------
   HubConfigurationAjaxController.inc.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Exceptions\CurlRequestException;
use \HubPublic\Http\CurlRequest;

/**
 * Class GambioHubConfigurationAjaxController
 *
 * This controller contains AJAX functionality related to the configuration pages of the Gambio Hub module.
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class HubConfigurationAjaxController extends AdminHttpViewController
{
    /**
     * Regular expression to verify a hexadecimal color value.
     *
     * @var string
     */
    protected $colorHexCodeRegex = '/^#[a-f0-9]{6}$/i';

    /**
     * @var array Extra features this hubConnector supports
     */
    protected $features = [
        'redirected_installation'
    ];


    /**
     * Get the shop data.
     *
     * If a Hub Client Key exists it will be returned instead of the shop data so that the merchant
     * data can be loaded from the customer portal.
     *
     * @return JsonHttpControllerResponse Returns the shop data.
     */
    public function actionGetShopData()
    {
        /**
         * @var CountryService  $countryService
         * @var CustomerService $customerService
         */
        $countryService  = StaticGXCoreLoader::getService('Country');
        $customerService = StaticGXCoreLoader::getService('Customer');

        $customerData = $customerService->getCustomerById(new IdType($_SESSION['customer_id']));

        // Authentication hash.
        $authHash = AuthHashCreator::create();
        $state    = STORE_ZONE ? (string)$countryService->getCountryZoneById(new IdType(STORE_ZONE))->getName() : '';

        $response = [
            'gender'      => (string)$customerData->getGender(),
            'firstname'   => TRADER_FIRSTNAME,
            'lastname'    => TRADER_NAME,
            'street'      => TRADER_STREET,
            'housenumber' => TRADER_STREET_NUMBER,
            'postcode'    => TRADER_ZIPCODE,
            'city'        => TRADER_LOCATION,
            'state'       => $state,
            'country'     => (string)$countryService->getCountryById(new IdType(STORE_COUNTRY))->getName(),
            'company'     => COMPANY_NAME,
            'email'       => (string)$customerData->getEmail(),
            'telephone'   => TRADER_TEL,
            'shopname'    => STORE_NAME,
            'shopkey'     => GAMBIO_SHOP_KEY,
            'authhash'    => $authHash->asString(),
            'version'     => ltrim(gm_get_conf('INSTALLED_VERSION'), 'v'),
            'url'         => HTTP_SERVER . DIR_WS_CATALOG,
            'features'    => $this->features
        ];

        try {
            $hubClientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');

            $hubClientKey = $hubClientKeyConfiguration->get();

            $response['clientkey'] = $hubClientKey;
        } catch (Exception $exception) {
            // The app will continue in demo mode.
        }

        return MainFactory::create('JsonHttpControllerResponse', $response);
    }


    /**
     * Starts the creation of the session key.
     *
     * @return JsonHttpControllerResponse Returns the session key data.
     */
    public function actionCreateSessionKey()
    {
        // Start a new gambio hub session.
        $serviceFactory         = MainFactory::create('HubServiceFactory');
        $sessionKeyService      = $serviceFactory->createHubSessionKeyService();
        $clientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');
        $curlRequest            = new CurlRequest();
        $logControl             = LogControl::get_instance();
        $hubSettings            = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        $sessionsApiClient      = MainFactory::create('HubSessionsApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL,
            $sessionKeyService, $clientKeyConfiguration, $curlRequest, $logControl, $hubSettings);
        $authHash               = AuthHashCreator::create();

        try {
            $shopUrl      = HTTP_SERVER . DIR_WS_CATALOG;
            $languageCode = new LanguageCode(new StringType(strtoupper(DEFAULT_LANGUAGE)));

            $response['gambio_hub_session_key'] = $sessionsApiClient->startSession($authHash, $shopUrl, $languageCode);
        } catch (UnexpectedValueException $exception) {
            // Send error.
            http_response_code(500);
            $response = AjaxException::response($exception);
        } catch (CurlRequestException $exception) {
            // Send error.
            http_response_code(500);
            $response = AjaxException::response($exception);
        }

        return MainFactory::create('JsonHttpControllerResponse', $response);
    }


    /**
     * Returns the Gambio Hub Translations for the frontend.
     *
     * @return JsonHttpControllerResponse Returns the translation data.
     */
    public function actionGetTranslations()
    {
        try {
            $languageTextManager = MainFactory::create('LanguageTextManager', 'gambio_hub_account',
                $_SESSION['languages_id']);
            $response            = $languageTextManager->get_section_array();
        } catch (Exception $exception) {
            http_response_code(500);
            $response = AjaxException::response($exception);
        }

        return MainFactory::create('JsonHttpControllerResponse', $response);
    }


    /**
     * Returns all available order statuses and the default one.
     *
     * @return JsonHttpControllerResponse Returns the order statuses data.
     */
    public function actionGetOrderStatuses()
    {
        // Response.
        $response = [];

        // Order status key-value array (ID is the key, and name is the value).
        $availableOrderStatuses = [];

        // CodeIgniter query builder.
        $queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();

        // Language ID.
        $languageId = (int)$_SESSION['languages_id'];

        // Order statuses fetch query.
        $orderStatuses = $queryBuilder->get_where('orders_status', ['language_id' => $languageId])->result_array();

        // Iterate over each query result row and append the ID and name to the order status array.
        foreach ($orderStatuses as $orderStatus) {
            // Order status ID.
            $id = (int)$orderStatus['orders_status_id'];

            // Order status name.
            $name = (string)$orderStatus['orders_status_name'];

            // Append data to order status array.
            $availableOrderStatuses[$id] = $name;
        }

        // Append data to response.
        $response['default'] = (int)DEFAULT_ORDERS_STATUS_ID;
        $response['all']     = $availableOrderStatuses;

        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Returns the style edit configuration values.
     *
     * @return JsonHttpControllerResponse Style edit configuration values.
     */
    public function actionGetStyleEditConfiguration()
    {
        $requiredSassVariables = ['brand-primary', 'gx-brand-secondary', 'gx-btn-primary-color', 'border-radius-base'];
        $variableAliases       = ['gx-btn-primary-color' => 'btn-primary-color'];
        $fallbackVariables     = ['gx-btn-primary-color' => 'brand-primary'];
        
        try {
            foreach ($requiredSassVariables as $requiredSassVariable) {
                $entries[] = [
                    'name' => $requiredSassVariable,
                    'value' => $requiredSassVariable
                ];
            }
            $entries = $this->resolveSettingValuesByNameRecursively($entries);
            $entries = $this->resolveUnresolvedSassVariablesToFallback($entries, $fallbackVariables);
            $entries = $this->duplicateSassVariablesWithAlias($entries, $variableAliases);
            
        } catch (Exception $exception) {
            $entries = [];
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $entries);
    }
    
    
    /**
     * Resolves settings from StyleEdit 4 with a certain depth
     *
     * @param array $variables
     * @param int   $depth
     * @param array $resolvedEntries
     * @param array $unresolvedEntries
     *
     * @return array
     */
    private function resolveSettingValuesByNameRecursively(
        array $variables,
        int $depth = 5,
        array $resolvedEntries = [],
        array $unresolvedEntries = []
    ): array {
        if (!$depth || (!count($unresolvedEntries) && $depth !==5)) {
            return array_merge($resolvedEntries, $unresolvedEntries);
        }
        
        $themeId         = StaticGXCoreLoader::getThemeControl()->getCurrentTheme();
        $styleEditReader = StyleEditServiceFactory::service()->getStyleEditReader($themeId);
        
        $settingValues = $styleEditReader->findSettingValuesByNames(...array_column($variables, 'name'));
        
        foreach($variables as $index => ['name' => $name, 'value' => $value]) {
            $entries[] = ['name' => $name, 'value' => $settingValues[$value] ?? null];
        }
        
        $sortedEntries = $this->sortSassVariablesInResolvedAndUnresolved($entries ?? []);
        
        return $this->resolveSettingValuesByNameRecursively(
            $sortedEntries['unresolved'],
            $depth - 1,
            array_merge($resolvedEntries, $sortedEntries['resolved']),
            $sortedEntries['unresolved']
        );
    }
    
    
    /**
     * Attempt to fallback to sass variables if the sass variable could not be resolved
     *
     * @param array $entries
     * @param array $fallbacks
     *
     * @return array
     */
    private function resolveUnresolvedSassVariablesToFallback(array $entries, array $fallbacks): array
    {
        $themeId         = StaticGXCoreLoader::getThemeControl()->getCurrentTheme();
        $styleEditReader = StyleEditServiceFactory::service()->getStyleEditReader($themeId);
        $sortedEntries   = $this->sortSassVariablesInResolvedAndUnresolved($entries);
        
        foreach ($sortedEntries['resolved'] as $index => ['value' => $value, 'name' => $name]) {
            
            if ($value === null && isset($fallbacks[$name])) {
                $sortedEntries['resolved'][$index] = [
                    'name'  => $name,
                    'value' => $styleEditReader->findSettingValueByName($fallbacks[$name]),
                ];
                unset($sortedEntries['unresolved'][$index]);
            }
        }
        
        return array_merge($sortedEntries['resolved'], $sortedEntries['unresolved']);
    }
    
    
    /**
     * Duplicates entries with the alias name
     *
     * @param array $entries
     * @param array $aliases
     *
     * @return array
     */
    private function duplicateSassVariablesWithAlias(array $entries, array $aliases): array
    {
        foreach ($entries as ['value' => $value, 'name' => $name]) {
            if (array_key_exists($name, $aliases)) {
                $entries[] = [
                    'name'  => $aliases[$name],
                    'value' => $value
                ];
            }
        }
        
        return $entries;
    }
    
    
    /**
     * Sort the sass variables in resolved and unresolved variables
     *
     * @param array $configurations
     *
     * @return array
     */
    private function sortSassVariablesInResolvedAndUnresolved(array $configurations): array
    {
        $sortedEntries = [
            'resolved'   => [],
            'unresolved' => []
        ];
        
        foreach ($configurations as $configuration) {
            if (preg_match('/^\$/', (string)$configuration['value'])) {
                $configuration['value']      = substr($configuration['value'], 1);
                $sortedEntries['unresolved'][] = $configuration;
            } else {
                $sortedEntries['resolved'][] = $configuration;
            }
        }
        
        return $sortedEntries;
    }

    /**
     * Deletes the Gambio Hub Client Key
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionDeleteClientKey()
    {
        gm_set_conf('GAMBIO_HUB_CLIENT_KEY', '');

        $queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();

        if ($queryBuilder->table_exists('configuration')) {
            $queryBuilder->update('configuration', ['configuration_value' => 'False'],
                ['configuration_key' => 'MODULE_PAYMENT_GAMBIO_HUB_STATUS']);
        } else {
            $queryBuilder->update('gx_configurations', ['value' => 'False'],
                ['key' => 'configuration/MODULE_PAYMENT_GAMBIO_HUB_STATUS']);
        }

        return MainFactory::create('JsonHttpControllerResponse', ['success' => true]);
    }
}
