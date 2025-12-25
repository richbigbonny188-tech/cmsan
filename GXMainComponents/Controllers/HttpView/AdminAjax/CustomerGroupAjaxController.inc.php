<?php

/* --------------------------------------------------------------
  CustomerGroupAjaxController.inc 2018-08-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class CustomerGroupAjaxController
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 * @extends    AdminHttpViewController
 */
class CustomerGroupAjaxController extends AdminHttpViewController
{
    
    /**
     * @var \CustomerGroupReadService
     */
    protected $customerGroupReadService;
    
    /**
     * @var \CustomerGroupWriteService
     */
    protected $customerGroupWriteService;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * Initialization of customer group controller
     */
    public function init()
    {
        $this->languageProvider          = MainFactory::create('LanguageProvider',
                                                               StaticGXCoreLoader::getDatabaseQueryBuilder());
        $this->customerGroupReadService  = StaticGXCoreLoader::getService('CustomerGroupRead');
        $this->customerGroupWriteService = StaticGXCoreLoader::getService('CustomerGroupWrite');
    }
    
    
    /**
     * Creates an entity in the database.
     *
     * @return bool
     */
    public function actionStore()
    {
        $result = $this->_storeCustomerGroup($this->customerGroupReadService->create());
        
        return MainFactory::create('JsonHttpControllerResponse', $result);
    }
    
    
    /**
     * Updates a customer entity in the database.
     *
     * @return bool
     */
    public function actionUpdate()
    {
        $result = $this->_updateCustomerGroup($this->customerGroupReadService->getById(new IdType($this->_getPostData('id'))));
        
        return MainFactory::create('JsonHttpControllerResponse', $result);
    }
    
    
    /**
     * Deletes a customer group entity in the database.
     *
     * @return bool
     */
    public function actionDelete()
    {
        $this->customerGroupWriteService->delete($this->_getCustomerGroupById('post'));
        
        return MainFactory::create('JsonHttpControllerResponse', ['success' => true]);
    }
    
    
    /**
     * Returns a customer group entity name and id by id.
     *
     * @return bool
     */
    public function actionGetNameById()
    {
        return MainFactory::create('JsonHttpControllerResponse',
                                   $this->_serializeCustomerGroup($this->_getCustomerGroupById()));
    }
    
    
    /**
     * Returns all data from one customer group by id.
     *
     * @return bool
     */
    public function actionGetById()
    {
        $customerGroup      = $this->_getCustomerGroupById();
        $customerGroupData  = $this->_serializeCustomerGroup($customerGroup);
        $settingsData       = $this->_serializeCustomerGroupSettings($customerGroup->getSettings());
        $configurationsData = $this->_serializeCustomerGroupConfigurations($customerGroup->getConfigurations());
        
        $data = array_merge($customerGroupData, $settingsData, $configurationsData);
        
        return MainFactory::create('JsonHttpControllerResponse', $data);
    }
    
    
    /**
     * Gets all customer group entity's from the database.
     *
     * @return bool
     */
    public function actionGetData()
    {
        return MainFactory::create('JsonHttpControllerResponse',
                                   $this->_serializeCustomerGroupCollection($this->customerGroupReadService->getAll()));
    }
    
    
    /**
     * Tries to store a customer group entity.
     *
     * @param \CustomerGroupInterface $customerGroup to store it in the database.
     *
     * @return array
     */
    protected function _storeCustomerGroup(CustomerGroupInterface $customerGroup)
    {
        
        try {
            $this->_prepareCustomerGroup($customerGroup)->_createBaseForCustomerGroup($customerGroup->store());
            
            $result = [
                'success' => true
            ];
        } catch (Exception $e) {
            $result = [
                'success' => false,
                'msg'     => $e->getMessage()
            ];
        }
        
        return $result;
    }
    
    
    /**
     * Stores posted customer group configurations to the customer group entity.
     *
     * @param \CustomerGroupInterface $customerGroup to set configurations on it.
     *
     * @param array                   $postData
     *
     * @return $this
     */
    protected function _storeCustomerGroupConfigurations(CustomerGroupInterface $customerGroup, array $postData)
    {
        $customerGroup->setConfigurations($this->customerGroupWriteService->getFactory()
                                              ->createConfigurations($this->_getDecimalType($postData['discount_price']),
                                                                     $this->_getDecimalType($postData['order_discount_price']),
                                                                     $this->_getOrderOption($postData['min_order']),
                                                                     $this->_getOrderOption($postData['max_order']),
                                                                     $this->_getStringArray($postData['payment_unallowed']),
                                                                     $this->_getStringArray($postData['shipping_unallowed'])));
        
        return $this;
    }
    
    
    /**
     * Stores posted customer group settings to the customer group entity.
     *
     * @param \CustomerGroupInterface $customerGroup to set settings on it.
     *
     * @param array                   $postData
     *
     * @return $this
     */
    protected function _storeCustomerGroupSettings(CustomerGroupInterface $customerGroup, array $postData)
    {
        $customerGroup->setSettings($this->customerGroupWriteService->getFactory()
                                        ->createSettings(new BoolType($postData['public']),
                                                         new BoolType($postData['order_discount']),
                                                         new BoolType($postData['graduated_prices']),
                                                         new BoolType($postData['customer_show_price']),
                                                         new BoolType($postData['show_add_tax']),
                                                         new BoolType($postData['add_tax']),
                                                         new BoolType($postData['discount_attributes']),
                                                         new BoolType($postData['fsk18_purchasable']),
                                                         new BoolType($postData['fsk18_display']),
                                                         new BoolType($postData['write_reviews']),
                                                         new BoolType($postData['read_reviews'])));
        
        return $this;
    }
    
    
    /**
     * Stores all names by language id in the given post data.
     *
     * @param \CustomerGroupInterface $customerGroup to set name.
     *
     * @param array                   $postData
     *
     * @return $this
     */
    protected function _storeCustomerGroupNames(CustomerGroupInterface $customerGroup, array $postData)
    {
        $languageIds       = $this->languageProvider->getIds()->getIntArray();
        $defaultLanguageId = $this->languageProvider->getDefaultLanguageId();
        $defaultName       = isset($postData['customer_group'][(string)$defaultLanguageId]) ? $postData['customer_group'][(string)$defaultLanguageId] : 'Group name';
        foreach ($languageIds as $languageId) {
            try {
                $oldName = $customerGroup->getName($this->languageProvider->getCodeById(new IdType($languageId)));
            } catch (InvalidArgumentException $e) {
                $oldName = false;
            }
            $groupName = $oldName ? : $defaultName;
            if (isset($postData['customer_group'][(string)$languageId])) {
                $groupName = $postData['customer_group'][(string)$languageId];
            }
            $customerGroup->setName(new StringType(strip_tags($groupName)),
                                    $this->languageProvider->getCodeById(new IdType($languageId)));
        }
        
        return $this;
    }
    
    
    /**
     * Returns a decimal type from given decimal sting.
     *
     * @param $decimalString
     *
     * @return \DecimalType
     */
    protected function _getDecimalType($decimalString)
    {
        return new DecimalType((string)$decimalString === '' ? '00.00' : $decimalString);
    }
    
    
    /**
     * Returns a decimal type, if the string is empty, it returns null.
     *
     * @param $orderString
     *
     * @return \DecimalType|null
     */
    protected function _getOrderOption($orderString)
    {
        return (string)$orderString === '' ? null : new DecimalType($orderString);
    }
    
    
    /**
     * Returns an Array from String, if the string is empty, it returns an empty string.
     *
     * @param $stringValue
     *
     * @return array
     */
    protected function _getStringArray($stringValue)
    {
        return (string)$stringValue === '' ? [] : explode(',', $stringValue);
    }
    
    
    /**
     * Tries to update a customer group entity.
     *
     * @param \CustomerGroupInterface $customerGroup to update it in the database.
     *
     * @return array
     */
    protected function _updateCustomerGroup(CustomerGroupInterface $customerGroup)
    {
        
        try {
            $this->_prepareCustomerGroup($customerGroup);
            $customerGroup->update();
            
            $result = [
                'success' => true
            ];
        } catch (Exception $e) {
            $result = [
                'success' => false,
                'msg'     => $e->getMessage()
            ];
        }
        
        return $result;
    }
    
    
    /**
     * Returns a customer group entity by post data id.
     *
     * @param string $type
     *
     * @return \CustomerGroup entity.
     */
    protected function _getCustomerGroupById($type = 'get')
    {
        $id = ($type === 'post') ? $this->_getPostData('id') : $this->_getQueryParameter('id');
        
        return $this->customerGroupReadService->getById(new IdType($id));
    }
    
    
    /**
     * Serializes customer group entities.
     *
     * @param \CustomerGroupInterface $customerGroup to serialize data.
     *
     * @return array serialized customer group array.
     */
    protected function _serializeCustomerGroup(CustomerGroupInterface $customerGroup)
    {
        return [
            'id'      => $customerGroup->getId(),
            'name'    => $customerGroup->getNames(),
            'default' => $customerGroup->isDefault(),
            'members' => $customerGroup->getMembers()
        ];
    }
    
    
    /**
     * Serializes customer group settings value object.
     *
     * @param \CustomerGroupSettingsInterface $settings to serialize data.
     *
     * @return array serialized customer group settings array.
     */
    protected function _serializeCustomerGroupSettings(CustomerGroupSettingsInterface $settings)
    {
        return [
            'public'              => $settings->isPublic(),
            'order_discount'      => $settings->isOtDiscountFlag(),
            'graduated_prices'    => $settings->isGraduatedPrices(),
            'customer_show_price' => $settings->isShowPrice(),
            'show_add_tax'        => $settings->isShowPriceTax(),
            'add_tax'             => $settings->isAddTaxOt(),
            'discount_attributes' => $settings->isDiscountAttributes(),
            'fsk18_purchasable'   => $settings->isFsk18Purchasable(),
            'fsk18_display'       => $settings->isFsk18Display(),
            'write_reviews'       => $settings->isWriteReviews(),
            'read_reviews'        => $settings->isReadReviews()
        ];
    }
    
    
    /**
     * Serializes customer group configurations value object.
     *
     * @param \CustomerGroupConfigurationsInterface $configurations to serialize data.
     *
     * @return array serialized customer group configurations array.
     */
    protected function _serializeCustomerGroupConfigurations(CustomerGroupConfigurationsInterface $configurations)
    {
        return [
            'discount_price'       => (string)$configurations->getDiscount(),
            'order_discount_price' => (string)$configurations->getOtDiscount(),
            'min_order'            => (string)$configurations->getMinOrder(),
            'max_order'            => (string)$configurations->getMaxOrder(),
            'payment_unallowed'    => implode(',', $configurations->getUnallowedPaymentModules()),
            'shipping_unallowed'   => implode(',', $configurations->getUnallowedShippingModules()),
        ];
    }
    
    
    /**
     *  Serializes customer group collections.
     *
     * @param \CustomerGroupCollection $customerGroupCollection to be serialized.
     *
     * @return array serialized customer group collection array
     */
    protected function _serializeCustomerGroupCollection(CustomerGroupCollection $customerGroupCollection)
    {
        $data = [];
        
        foreach ($customerGroupCollection->getArray() as $customerGroup) {
            /* @var $customerGroup \CustomerGroupInterface */
            $customerGroupData  = $this->_serializeCustomerGroup($customerGroup);
            $settingsData       = $this->_serializeCustomerGroupSettings($customerGroup->getSettings());
            $configurationsData = $this->_serializeCustomerGroupConfigurations($customerGroup->getConfigurations());
            
            $data[] = array_merge($customerGroupData, $settingsData, $configurationsData);
        }
        
        return $data;
    }
    
    
    /**
     * Sets default value to customer group entity.
     *
     * @param \CustomerGroupInterface $customerGroup
     * @param                         $postData
     *
     * @return $this
     */
    protected function _setAsDefault(CustomerGroupInterface $customerGroup, $postData)
    {
        $customerGroup->setDefault(new BoolType($postData['default']));
        
        return $this;
    }
    
    
    /**
     * Prepares a customer group entity.
     *
     * @param \CustomerGroupInterface $customerGroup
     *
     * @return \CustomerGroupAjaxController
     */
    protected function _prepareCustomerGroup(CustomerGroupInterface $customerGroup)
    {
        $postData = $this->_getPostDataCollection()->getArray();
        
        $this->_storeCustomerGroupNames($customerGroup, $postData)
            ->_storeCustomerGroupSettings($customerGroup,
                                          $postData)
            ->_storeCustomerGroupConfigurations($customerGroup, $postData)
            ->_setAsDefault($customerGroup, $postData);
        
        return $this;
    }
    
    
    /**
     * Creates a base personal offer table if base is not an admin.
     *
     * @param \customerGroupInterface $customerGroup
     *
     * @return $this
     */
    protected function _createBaseForCustomerGroup(customerGroupInterface $customerGroup)
    {
        if ($this->_getPostData('base') > 0) {
            $customerGroup->createBase(new IntType($this->_getPostData('base')));
        }
        
        return $this;
    }
}
