<?php

/* --------------------------------------------------------------
 JSWidgetsAjaxController.inc.php 2018-09-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

MainFactory::load_class('AdminHttpViewController');

/**
 * Class JSWidgetsAjaxController
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class JSWidgetsAjaxController extends AdminHttpViewController
{
    public function actionIconInput()
    {
        $languageProvider = MainFactory::create('LanguageProvider', StaticGXCoreLoader::getDatabaseQueryBuilder());
        $languageId       = $this->_getQueryParameter('language_id');
        $langId           = new IdType($languageId);
        if (defined('ENABLE_SSL_CATALOG') && (string)ENABLE_SSL_CATALOG === 'true') {
            $urlPrefix = HTTPS_CATALOG_SERVER;
        } else {
            $urlPrefix = HTTP_CATALOG_SERVER;
        }
        $iconUrl = $urlPrefix . DIR_WS_CATALOG . 'images/flags/4x3/'
                   . strtolower($languageProvider->getCodeById($langId)) . '.svg';
        
        return new JsonHttpControllerResponse([
                                                  'iconUrl' => $iconUrl
                                              ]);
    }
    
    
    /**
     * Detects if the file manager is installed or not.
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionResponsiveFileManagerExists()
    {
        return MainFactory::create('JsonHttpControllerResponse',
                                   ['exists' => is_dir(DIR_FS_CATALOG . 'ResponsiveFilemanager')]);
    }
    
    
    /**
     * Checks if the group check is enabled.
     * If true, the response provide information about the existing customer groups.
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionIsGroupCheckEnabled()
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        
        $status = $db->from('gx_configurations')
                      ->where('key', 'configuration/GROUP_CHECK')
                      ->get()
                      ->row_array()['value'] === 'true';
        
        $response = ['status' => $status];
        if ($status) {
            /** @var \CustomerGroupReadServiceInterface $customerGroupReadService */
            $customerGroupReadService   = StaticGXCoreLoader::getService('CustomerGroupRead');
            $customerGroups             = $customerGroupReadService->getAll();
            $response['customerGroups'] = $this->_serializeCustomerGroupCollection($customerGroups);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Serializes customer group collections.
     * @TODO: Replace the methods with the customer group serializer, once it is finished.
     *
     * @param \CustomerGroupCollection $customerGroupCollection Customer group collection to be serialized.
     *
     * @return array Serialized customer group collection array.
     */
    protected function _serializeCustomerGroupCollection(CustomerGroupCollection $customerGroupCollection)
    {
        $data = [];
        foreach ($customerGroupCollection->getArray() as $customerGroup) {
            $data[] = $this->_serializeCustomerGroup($customerGroup);
        }
        
        return $data;
    }
    
    
    /**
     * Serializes customer group entities.
     * @TODO: Replace the methods with the customer group serializer, once it is finished.
     *
     * @param \CustomerGroupInterface $customerGroup Customer group entity to be serialized.
     *
     * @return array Serialized customer group array.
     */
    protected function _serializeCustomerGroup(CustomerGroupInterface $customerGroup)
    {
        return [
            'id'             => $customerGroup->getId(),
            'names'          => $this->_serializeCustomerGroupNames($customerGroup->getNames()),
            'settings'       => $this->_serializeCustomerGroupSettings($customerGroup->getSettings()),
            'configurations' => $this->_serializeCustomerGroupConfigurations($customerGroup->getConfigurations())
        ];
    }
    
    
    /**
     * Serializes customer group names array.
     * @TODO: Replace the methods with the customer group serializer, once it is finished.
     *
     * @param array $customerGroupNames Customer group names array to be serialized.
     *
     * @return array Serialized customer group names array.
     */
    protected function _serializeCustomerGroupNames(array $customerGroupNames)
    {
        $data = [];
        foreach ($customerGroupNames as $languageCode => $name) {
            $data[$languageCode] = $name;
        }
        
        return $data;
    }
    
    
    /**
     * Serializes customer group settings value objects.
     * @TODO: Replace the methods with the customer group serializer, once it is finished.
     *
     * @param \CustomerGroupSettingsInterface $settings Customer group settings object to be serialized.
     *
     * @return array Serialized customer group settings array.
     */
    protected function _serializeCustomerGroupSettings(CustomerGroupSettingsInterface $settings)
    {
        return [
            'public'             => $settings->isPublic(),
            'otDiscountFlag'     => $settings->isOtDiscountFlag(),
            'graduatedPrices'    => $settings->isGraduatedPrices(),
            'showPrice'          => $settings->isShowPrice(),
            'showPriceTax'       => $settings->isShowPriceTax(),
            'addTaxOt'           => $settings->isAddTaxOt(),
            'discountAttributes' => $settings->isDiscountAttributes(),
            'fsk18Purchasable'   => $settings->isFsk18Purchasable(),
            'fsk18Display'       => $settings->isFsk18Display(),
            'writeReviews'       => $settings->isWriteReviews(),
            'readReviews'        => $settings->isReadReviews()
        ];
    }
    
    
    /**
     * Serializes customer group configuration value objects.
     * @TODO: Replace the methods with the customer group serializer, once it is finished.
     *
     * @param \CustomerGroupConfigurationsInterface $configurations Customer group configuration object to be
     *                                                              serialized.
     *
     * @return array Serialized customer group configuration array.
     */
    protected function _serializeCustomerGroupConfigurations(CustomerGroupConfigurationsInterface $configurations)
    {
        return [
            'minOrder'                 => $configurations->getMinOrder(),
            'maxOrder'                 => $configurations->getMaxOrder(),
            'discount'                 => $configurations->getDiscount(),
            'otDiscount'               => $configurations->getOtDiscount(),
            'unallowedPaymentModules'  => $configurations->getUnallowedPaymentModules(),
            'unallowedShippingModules' => $configurations->getUnallowedShippingModules()
        ];
    }
}
