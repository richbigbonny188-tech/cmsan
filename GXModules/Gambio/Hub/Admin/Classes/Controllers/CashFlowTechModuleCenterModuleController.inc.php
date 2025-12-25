<?php
/* --------------------------------------------------------------
   CashFlowTechModuleCenterModuleController.inc.php 2019-07-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Http\CurlRequest;

class CashFlowTechModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    protected $templatesBaseDir;
    protected $hubAssetHelper;

    protected function _init()
    {
        $this->pageTitle = $this->languageTextManager->get_text('cashflowtech_mcm_title');
        $installedVersion          = gm_get_conf('INSTALLED_VERSION');
        $this->hubAssetHelper      = MainFactory::create('HubAssetHelper', $installedVersion);
        $this->templatesBaseDir = DIR_FS_CATALOG . $this->hubAssetHelper->getTemplatesBasePath();
    }
    
    
    /**
     * @return \AdminLayoutHttpControllerResponse|\AdminPageHttpControllerResponse|\RedirectHttpControllerResponse
     * @throws \Exception
     */
    public function actionDefault()
    {
        $hubClientKey = (string)gm_get_conf('GAMBIO_HUB_CLIENT_KEY');
        if (empty($hubClientKey)) {
            return $this->actionNoHubDefault();
        }

        try {
            $mandatorStatus = $this->getMandatorStatusFromHub();
        } catch (UnexpectedValueException $e) {
            return $this->actionHubUnavailable();
        }
        
        if ($mandatorStatus['hasMandatorId'] !== true) {
            return $this->actionOnboarding();
        }
    
        $template = MainFactory::create(
            'ExistingFile',
            new NonEmptyStringType($this->templatesBaseDir . '/cashflowtech_status.html')
        );
        $templateData = [
            'mandatorStatus'        => $mandatorStatus,
            'pageToken'             => $_SESSION['coo_page_token']->generate_token(),
        ];
    
        return new AdminLayoutHttpControllerResponse(
            new NonEmptyStringType($this->pageTitle),
            $template,
            new KeyValueCollection($templateData)
        );
    }
    
    protected function actionOnboarding()
    {
        $salutation_id = 0;
        if ($_SESSION['customer_gender'] === 'm') {
            $salutation_id = 2;
        } elseif ($_SESSION['customer_gender'] === 'f') {
            $salutation_id = 1;
        }
    
        $countryService = StaticGXCoreLoader::getService('Country');
        /** @var \CustomerCountryInterface $storeCountry */
        $storeCountry = $countryService->getCountryById(new IdType((int)@constant('STORE_COUNTRY')));
    
        $customerReadService = StaticGXCoreLoader::getService('CustomerRead');
        /** @var Customer $adminCustomer */
        $adminCustomer = $customerReadService->getCustomerById(new IdType((int)$_SESSION['customer_id']));
    
        $tosPhrase = $this->languageTextManager->get_text('cashflowtech_mcm_tos_text');
        $tosText = preg_replace('/\[tos:(.*?)\]/', '<a href="#" id="cft_tos_link">$1</a>', $tosPhrase);
        $tosText = preg_replace('/\[privacy:(.*?)\]/', '<a href="#" id="cft_privacy_link">$1</a>', $tosText);
    
        $template = MainFactory::create(
            'ExistingFile',
            new NonEmptyStringType(
                $this->templatesBaseDir . '/cashflowtech_configuration.html'
            )
        );
        $templateData = [
            'pageToken' => $_SESSION['coo_page_token']->generate_token(),
            'action_save_mandator' => xtc_href_link(
                'admin.php',
                'do=CashFlowTechModuleCenterModule/SaveMandator'
            ),
            'countries' => $this->getCountries(),
            'person_data' => [
                'salutation_id'            => $salutation_id,
                'title_id'                 => '0',
                'company'                  => (string)@constant('STORE_NAME'),
                'company_type_id'          => '0',
                'first_name'               => (string)@constant('TRADER_FIRSTNAME'),
                'last_name'                => (string)@constant('TRADER_NAME'),
                'maiden_name'              => '',
                'street'                   => (string)@constant('TRADER_STREET'),
                'housenumber'              => (string)@constant('TRADER_STREET_NUMBER'),
                'postalcode'               => (string)@constant('TRADER_ZIPCODE'),
                'city'                     => (string)@constant('TRADER_LOCATION'),
                'country'                  => $storeCountry->getIso2(),
                'day_of_birth'             => $adminCustomer->getDateOfBirth()->format('Y-m-d'),
                'email'                    => $adminCustomer->getEmail(),
                'phone'                    => $adminCustomer->getTelephoneNumber(),
                'mobile'                   => '',
                'fax'                      => $adminCustomer->getFaxNumber(),
                'account_number'           => '',
                'bank_number'              => '',
                'account_iban'             => '',
                'bank_bic'                 => '',
                'account_owner_first_name' => (string)@constant('TRADER_FIRSTNAME'),
                'account_owner_last_name'  => (string)@constant('TRADER_NAME'),
                'tax_id'                   => (string)@constant('STORE_OWNER_VAT_ID'),
                'trade_register_city'      => '',
                'trade_register_number'    => '',
            ],
            'tos_text' => $tosText,
        ];
    
        return new AdminLayoutHttpControllerResponse(
            new NonEmptyStringType($this->pageTitle),
            $template,
            new KeyValueCollection($templateData)
        );
    }
    
    protected function actionNoHubDefault()
    {
        $template = MainFactory::create(
            'ExistingFile',
            new NonEmptyStringType($this->templatesBaseDir . '/cashflowtech_nohub.html')
        );
        $templateData = [
            'hubconfig_url' => xtc_href_link('admin.php', 'do=HubConfiguration/account'),
        ];
        return new AdminLayoutHttpControllerResponse(
            new NonEmptyStringType($this->pageTitle),
            $template,
            new KeyValueCollection($templateData)
        );
    }
    
    protected function actionHubUnavailable()
    {
        $template = MainFactory::create(
            'ExistingFile',
            new NonEmptyStringType($this->templatesBaseDir . '/cashflowtech_nohub.html')
        );
        $templateData = [
            'errorMessage' =>  'Fehler bei der Kommunikation mit dem GambioHub',
            'hubconfig_url' => xtc_href_link('admin.php', 'do=HubConfiguration/account'),
        ];
        return new AdminLayoutHttpControllerResponse(
            new NonEmptyStringType($this->pageTitle),
            $template,
            new KeyValueCollection($templateData)
        );
    }
    

    protected function invokeHubAction($action, $data)
    {
        if(!is_array($data))
        {
            throw new RuntimeException('data must be of type array');
        }
        
        $data['clientkey'] = gm_get_conf('GAMBIO_HUB_CLIENT_KEY');
        
        /** @var HubSettings $hubSettings */
        $hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        /** @var \HubCallbackApiClient $hubCallbackApiClient */
        $hubCallbackApiClient = MainFactory::create(
            'HubCallbackApiClient',
            MODULE_PAYMENT_GAMBIO_HUB_URL,
            new CurlRequest(),
            LogControl::get_instance(),
            $hubSettings
        );
        
        /** @var \HubPublic\ValueObjects\HttpResponse $response */
        $response = $hubCallbackApiClient->execute(
            'CashFlowTechHub',
            true,
            ['action' => $action],
            $data
        );
        if (!in_array((int)$response->getStatusCode(), [200, 201], true))
        {
            $errorMessage = 'Error sending configuration to Hub';
            $responseBody = $response->getBody();
            if (!empty($responseBody)) {
                $responseJson = json_decode($responseBody, true);
                if (is_array($responseJson) && isset($responseJson['additional_status'])) {
                    $errorMessage .= ' (' . $responseJson['additional_status'] . ')';
                }
            }
            throw new RuntimeException($errorMessage);
        }
        return $response;
    }
    
    protected function getMandatorStatusFromHub()
    {
        /** @var HubSettings $hubSettings */
        $hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        /** @var \HubCallbackApiClient $hubCallbackApiClient */
        $hubCallbackApiClient = MainFactory::create(
            'HubCallbackApiClient',
            MODULE_PAYMENT_GAMBIO_HUB_URL,
            new CurlRequest(),
            LogControl::get_instance(),
            $hubSettings
        );
    
        /** @var \HttpResponse $response */
        $response = $hubCallbackApiClient->execute(
            'CashFlowTechHub',
            false,
            [
                'action' => 'CheckMandatorStatus',
                'clientkey' => gm_get_conf('GAMBIO_HUB_CLIENT_KEY')
            ]
        );
        if($response->getStatusCode() !== 200)
        {
            throw new RuntimeException('Error sending configuration to Hub');
        }
    
        $responseBody = json_decode($response->getBody(), true);
        return $responseBody;
    }
    
    protected function getCountries()
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $countries = $db->from('countries')
            ->where('status', 1)
            ->select('countries_iso_code_2, countries_name, countries_id')
            ->get()->result_array();
        return $countries;
    }
    
    public function actionSaveMandator()
    {
        $this->_validatePageToken();
        
        $confirmation = $this->_getPostData('confirmation');
        if (isset($confirmation['tos'])) {
            $personData = $this->_getPostData('person_data');
            try {
                $response = $this->invokeHubAction('CreateMandator', ['person_data' => json_encode($personData)]);
                $message = $this->languageTextManager->get_text('cashflowtech_mcm_mandator_created');
                $responseBody = $response->getBody();
                if (!empty($responseBody)) {
                    $responseJson = json_decode($responseBody, true);
                    if (is_array($responseJson) && !empty($responseJson['additional_status'])) {
                        $message .= ' (' . $responseJson['additional_status'] . ')';
                    }
                }
                $GLOBALS['messageStack']->add_session($message, 'info');
            } catch (Exception $e) {
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('cashflowtech_mcm_mandator_creation_error') . $e->getMessage(), 'info');
            }
        } else {
            $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('cashflowtech_mcm_confirmation_tos_required'), 'info');
        }
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=CashFlowTechModuleCenterModule'));
    }
}
