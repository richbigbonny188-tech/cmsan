<?php
/* --------------------------------------------------------------
   CustomerGroupsApiV2Controller.inc.php 2022-02-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CustomerGroupsApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var CustomerGroupReadServiceInterface
     */
    protected $readService;
    
    /**
     * @var CustomerGroupWriteServiceInterface
     */
    protected $writeService;
    
    
    /**
     * Initialize controller components.
     */
    protected function __initialize()
    {
        $this->readService  = StaticGXCoreLoader::getService('CustomerGroupRead');
        $this->writeService = StaticGXCoreLoader::getService('CustomerGroupWrite');
    }
    
    
    /**
     * @api             {get} /customer_groups/:id Get customer groups
     * @apiVersion      2.5.0
     * @apiName         GetCustomerGroups
     * @apiGroup        CustomerGroups
     *
     * @apiDescription
     * Get multiple or a single customer groups record through the GET method
     *
     * @apiExample {curl} Get All CustomerGroups records
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/customer_groups
     *
     * @apiExample {curl} Get CustomerGroups record With ID = 982
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/customer_groups/982
     *
     * @apiError        400-BadRequest CustomerGroups data were not provided or customer groups record ID was not
     *                  provided or is invalid.
     *
     * @apiErrorExample Error-Response (Missing or invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "CustomerGroups record ID was not provided or is invalid."
     * }
     *
     * @apiError        404-NotFound CustomerGroups record could not be found.
     *
     * @apiErrorExample Error-Response (CustomerGroups Not Found)
     * HTTP/1.1 404 Not Found
     * {
     *   "code": 404,
     *   "status": "error",
     *   "message": "CustomerGroups record could not be found."
     * }
     */
    public function get()
    {
        if (isset($this->uri[1]) && is_numeric($this->uri[1])) {
            try {
                $customerGroup = $this->readService->getById(new IntType((int)$this->uri[1]));
                
                return $this->_writeResponse($this->_serializeCustomerGroup($customerGroup));
            } catch (EntityNotFoundException $e) {
                throw new HttpApiV2Exception($e->getMessage(), 404, $e);
            }
        }
        
        if (isset($this->uri[1]) && !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Customer group record is was not provided', 400);
        }
        
        $response   = $this->_serializeCustomerGroupCollection($this->readService->getAll());
        $searchTerm = $this->request->getQueryParam('q') ?? $this->request->getQueryParam('search') ?? null;
        
        if ($searchTerm !== null) {
            $this->_searchResponse($response, $searchTerm);
        }
        
        $this->_sortResponse($response);
        $this->_paginateResponse($response);
        $this->_minimizeResponse($response);
        $this->_linkResponse($response);
        
        return $this->_writeResponse($response);
    }
    
    
    /**
     * @api             {post} /customer_groups Create CustomerGroups
     * @apiVersion      2.5.0
     * @apiName         CreateCustomerGroups
     * @apiGroup        CustomerGroups
     *
     * @apiDescription
     * This method enables the creation of a new CustomerGroups into the system.
     *
     * @apiParamExample {json} CustomerGroups entity
     *  {
     *      "names": {
     *          "EN": "Kids",
     *          "DE": "Kinder"
     *      },
     *      "settings": {
     *          "public": false,
     *          "otDiscountFlag": false,
     *          "graduatedPrices": false,
     *          "showPrice": true,
     *          "showPriceTax": false,
     *          "addTaxOt": false,
     *          "discountAttributes": false,
     *          "fsk18Purchasable": false,
     *          "fsk18": false,
     *          "fsk18Display": false,
     *          "writeReviews": false,
     *          "readReviews": false
     *      },
     *      "configurations": {
     *          "minOrder": 2.50,
     *          "maxOrder": 20.00,
     *          "discount": 0.5,
     *          "otDiscount": 0,
     *          "unallowedPaymentModules": [
     *              "paypal", "cod", "moneyorder"
     *          ],
     *          "unallowedShippingModules": [
     *              "selfpickup"
     *          ]
     *      }
     *  }
     *
     * @apiParam {Object} names Object with language code as keys and name as values.
     * @apiParam {Object} settings Object with customer group settings, visit the example for further information.
     * @apiParam {Object} configurations Object with customer group configurations, visit the example for further
     *           information.
     *
     * @apiSuccess (201) Request-Body If successful, this method returns the complete customerGroups resource
     * in the response body.
     *
     * @apiSuccessExample {json} Success-Response
     *  {
     *      "id": 2,
     *      "names": {
     *          "EN": "Kids",
     *          "DE": "Kinder"
     *      },
     *      "settings": {
     *          "public": false,
     *          "otDiscountFlag": false,
     *          "graduatedPrices": false,
     *          "showPrice": true,
     *          "showPriceTax": false,
     *          "addTaxOt": false,
     *          "discountAttributes": false,
     *          "fsk18Purchasable": false,
     *          "fsk18": false,
     *          "fsk18Display": false,
     *          "writeReviews": false,
     *          "readReviews": false
     *      },
     *      "configurations": {
     *          "minOrder": 2.50,
     *          "maxOrder": 20.00,
     *          "discount": 0.5,
     *          "otDiscount": 0,
     *          "unallowedPaymentModules": [
     *              "paypal", "cod", "moneyorder"
     *          ],
     *          "unallowedShippingModules": [
     *              "selfpickup"
     *          ]
     *      }
     *  }
     *
     * @apiError        400-BadRequest The body of the request was empty.
     * @apiErrorExample Error-Response
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "CustomerGroups data were not provided."
     * }
     */
    public function post()
    {
        $customerGroup = $this->_deserializeCustomerGroup(json_encode($this->request->getParsedBody()));
        $customerGroup->store();
        
        $this->_writeResponse($this->_serializeCustomerGroup($customerGroup));
    }
    
    
    /**
     * @api             {patch} /customer_groups/:id Updates CustomerGroup entity
     * @apiVersion      2.5.0
     * @apiName         UpdateCustomerGroups
     * @apiGroup        CustomerGroups
     *
     * @apiDescription
     * Use this method if you want to update an existing customer groups record.
     *
     * @apiParamExample {json} CustomerGroups entity
     * {
     *      "names": {
     *          "EN": "Kids",
     *          "DE": "Kinder"
     *      },
     *      "settings": {
     *          "public": false,
     *          "otDiscountFlag": false,
     *          "graduatedPrices": false,
     *          "showPrice": true,
     *          "showPriceTax": false,
     *          "addTaxOt": false,
     *          "discountAttributes": false,
     *          "fsk18Purchasable": false,
     *          "fsk18": false,
     *          "fsk18Display": false,
     *          "writeReviews": false,
     *          "readReviews": false
     *      },
     *      "configurations": {
     *          "minOrder": 2.50,
     *          "maxOrder": 20.00,
     *          "discount": 0.5,
     *          "otDiscount": 0,
     *          "unallowedPaymentModules": [
     *              "paypal", "cod", "moneyorder"
     *          ],
     *          "unallowedShippingModules": [
     *              "selfpickup"
     *          ]
     *      }
     * }
     *
     * @apiParam {Object} names Object with language code as keys and name as values.
     * @apiParam {Object} settings Object with customer group settings, visit the example for further information.
     * @apiParam {Object} configurations Object with customer group configurations, visit the example for further
     *           information.
     *
     * @apiSuccess (200) Request-Body If successful, this method returns the complete customerGroups resource
     * in the response body.
     *
     * @apiSuccessExample {json} Success-Response
     *  {
     *      "id": 2,
     *      "names": {
     *          "EN": "Kids",
     *          "DE": "Kinder"
     *      },
     *      "settings": {
     *          "public": false,
     *          "otDiscountFlag": false,
     *          "graduatedPrices": false,
     *          "showPrice": true,
     *          "showPriceTax": false,
     *          "addTaxOt": false,
     *          "discountAttributes": false,
     *          "fsk18Purchasable": false,
     *          "fsk18": false,
     *          "fsk18Display": false,
     *          "writeReviews": false,
     *          "readReviews": false
     *      },
     *      "configurations": {
     *          "minOrder": 2.50,
     *          "maxOrder": 20.00,
     *          "discount": 0.5,
     *          "otDiscount": 0,
     *          "unallowedPaymentModules": [
     *              "paypal", "cod", "moneyorder"
     *          ],
     *          "unallowedShippingModules": [
     *              "selfpickup"
     *          ]
     *      }
     *  }
     *
     * @apiError        400-BadRequest CustomerGroups data were not provided or customerGroups record ID was not
     *                  provided or is invalid.
     *
     * @apiErrorExample Error-Response (Empty request body)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "CustomerGroups data were not provided."
     * }
     *
     * @apiErrorExample Error-Response (Missing or invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "CustomerGroups record ID was not provided or is invalid."
     * }
     *
     * @apiError        404-NotFoundRequest CustomerGroups data were not provided or customerGroups record ID was not
     *                  provided or is invalid.
     *
     * @apiErrorExample Error-Response (Entity not found)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 404,
     *   "status": "error",
     *   "message": "Customer group entity was not found with provided id [ID]"
     * }
     */
    public function patch()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Customer group record ID was not provided or is invalid in the Request-URI');
        }
        
        $requestBody = json_encode($this->request->getParsedBody());
        if (empty($requestBody) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Customer group data were not provided.', 400);
        }
        
        try {
            $customerGroup = $this->_deserializeCustomerGroup($requestBody, $this->uri[1]);
            $customerGroup->update();
            $this->_writeResponse($this->_serializeCustomerGroup($customerGroup));
        } catch (EntityNotFoundException $e) {
            throw new HttpApiV2Exception($e->getMessage(), 404, $e);
        }
    }
    
    
    /**
     * @api        {delete} /customer_groups/:id Delete CustomerGroups
     * @apiVersion 2.5.0
     * @apiName    DeleteCustomerGroups
     * @apiGroup   CustomerGroups
     *
     * @apiDescription
     * Removes a customer groups record from the system. This method will always return success
     * even if the customer group does not exist.
     *
     * @apiExample {curl} Delete CustomerGroups with ID = 84
     *             curl -X DELETE --user admin@example.org:12345 https://example.org/api.php/v2/customer_groups/84
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *   "code": 200,
     *   "status": "success",
     *   "action": "delete",
     *   "customerGroupId": 84
     * }
     */
    public function delete()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Customer group record ID was not provided or is invalid in the Request-URI');
        }
        
        $customerGroup = $this->readService->getById(new IdType($this->uri[1]));
        $customerGroup->delete();
        
        $this->_writeResponse([
                                  'code'            => 200,
                                  'status'          => 'success',
                                  'action'          => 'delete',
                                  'customerGroupId' => $customerGroup->getId(),
                              ]);
    }
    
    
    /**
     * Serializes customer group collections.
     *
     * @param CustomerGroupCollection $customerGroupCollection Customer group collection to be serialized.
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
     *
     * @param CustomerGroupInterface $customerGroup Customer group entity to be serialized.
     *
     * @return array Serialized customer group array.
     */
    protected function _serializeCustomerGroup(CustomerGroupInterface $customerGroup)
    {
        return [
            'id'             => $customerGroup->getId(),
            'names'          => $this->_serializeCustomerGroupNames($customerGroup->getNames()),
            'settings'       => $this->_serializeCustomerGroupSettings($customerGroup->getSettings()),
            'configurations' => $this->_serializeCustomerGroupConfigurations($customerGroup->getConfigurations()),
        ];
    }
    
    
    /**
     * Deserialize a json string to a customer unit entity.
     *
     * @param string   $customerGroupJson Customer group json to be serialized.
     * @param int|null $id                (Optional) Id of entity to be deserialize.
     *
     * @return QuantityUnitInterface Deserialize customer group entity.
     */
    protected function _deserializeCustomerGroup($customerGroupJson, $id = null)
    {
        $customerGroupData = json_decode($customerGroupJson, true);
        $customerGroup     = $id ? $this->readService->getById(new IntType($id)) : $this->readService->create();
        
        foreach ($customerGroupData['names'] as $languageCode => $name) {
            $customerGroup->setName(new StringType($name), new LanguageCode(new StringType($languageCode)));
        }
        
        $customerGroup->setSettings($this->_deserializeCustomerGroupSettings($customerGroupData['settings']));
        $customerGroup->setConfigurations($this->_deserializeCustomerGroupConfigurations($customerGroupData['configurations']));
        
        return $customerGroup;
    }
    
    
    /**
     * Serializes customer group names array.
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
     *
     * @param CustomerGroupSettingsInterface $settings Customer group settings object to be serialized.
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
            'fsk18'              => $settings->isFsk18Purchasable(),
            'fsk18Display'       => $settings->isFsk18Display(),
            'writeReviews'       => $settings->isWriteReviews(),
            'readReviews'        => $settings->isReadReviews(),
        ];
    }
    
    
    /**
     * Deserialize customer group settings.
     *
     * @param array $settings Customer group settings to be serialzed.
     *
     * @return CustomerGroupSettingsInterface Deserialize customer group settings.
     */
    protected function _deserializeCustomerGroupSettings(array $settings)
    {
        return $this->writeService->getFactory()->createSettings(new BoolType($settings['public']),
                                                                 new BoolType($settings['otDiscountFlag']),
                                                                 new BoolType($settings['graduatedPrices']),
                                                                 new BoolType($settings['showPrice']),
                                                                 new BoolType($settings['showPriceTax']),
                                                                 new BoolType($settings['addTaxOt']),
                                                                 new BoolType($settings['discountAttributes']),
                                                                 new BoolType(isset($settings['fsk18Purchasable']) ? $settings['fsk18Purchasable'] : $settings['fsk18']),
                                                                 new BoolType($settings['fsk18Display']),
                                                                 new BoolType($settings['writeReviews']),
                                                                 new BoolType($settings['readReviews']));
    }
    
    
    /**
     * Serializes customer group configuration value objects.
     *
     * @param CustomerGroupConfigurationsInterface $configurations  Customer group configuration object to be
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
            'unallowedShippingModules' => $configurations->getUnallowedShippingModules(),
        ];
    }
    
    
    /**
     * Deserialize customer group configurations.
     *
     * @param array $configurations Customer group configurations to be serialzed.
     *
     * @return CustomerGroupConfigurationsInterface Deserialize customer group configurations.
     */
    protected function _deserializeCustomerGroupConfigurations(array $configurations)
    {
        return $this->writeService->getFactory()->createConfigurations(new DecimalType($configurations['discount']),
                                                                       new DecimalType($configurations['otDiscount']),
                                                                       $configurations['minOrder'] ? new DecimalType($configurations['minOrder']) : null,
                                                                       $configurations['maxOrder'] ? new DecimalType($configurations['maxOrder']) : null,
                                                                       $configurations['unallowedPaymentModules'],
                                                                       $configurations['unallowedShippingModules']);
    }
}
