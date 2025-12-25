<?php
/* --------------------------------------------------------------
   HubCheckoutHelper.inc.php 2023-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\Collections\CartItemCollection;
use \HubPublic\Collections\CartTotalItemCollection;
use \HubPublic\Exceptions\CurlRequestException;
use \HubPublic\Http\CurlRequest;
use \HubPublic\Serializers\CartContentSerializer;
use \HubPublic\Serializers\CustomerInformationSerializer;
use \HubPublic\Serializers\HubClientInformationSerializer;
use \HubPublic\Serializers\OrderContentSerializer;
use \HubPublic\Serializers\ClientSessionInformationSerializer;
use \HubPublic\ValueObjects\Builder\CustomerInformation as CustomerInformationBuilder;
use \HubPublic\ValueObjects\CartContent;
use \HubPublic\ValueObjects\CartItem;
use \HubPublic\ValueObjects\CartTotalItem;
use \HubPublic\ValueObjects\HubClientInformation;
use \HubPublic\ValueObjects\HubClientKey;
use \HubPublic\ValueObjects\HubSessionKey;
use \HubPublic\ValueObjects\ClientSessionInformation;

/**
 * Class HubCheckoutHelper
 */
class HubCheckoutHelper
{
    /**
     * Creates a HubTransactionsApiClient instance.
     *
     * @param \HubPublic\ValueObjects\HubSessionKey $sessionKey Hub session key.
     *
     * @return \HubTransactionsApiClient Created instance.
     */
    public function createHubTransactionsApiClient(HubSessionKey $sessionKey)
    {
        // cURL request.
        $request = new CurlRequest();
        
        // Cart content serializer.
        $cartContentSerializer = new CartContentSerializer();
        
        // Customer information serializer.
        $customerInformationSerializer = new CustomerInformationSerializer();
        
        // Hub client information serializer.
        $hubClientInformationSerializer = new HubClientInformationSerializer();
        
        // Session information serializer.
        $clientSessionInformationSerializer = new ClientSessionInformationSerializer();
        
        // Order content serializer.
        $orderContentSerializer = new OrderContentSerializer($customerInformationSerializer);
        
        // Shop logger instance.
        $logControl = LogControl::get_instance();
        
        // Hub settings instance.
        $hubSettings = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        
        return MainFactory::create('HubTransactionsApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL, $sessionKey, $request,
            $cartContentSerializer, $customerInformationSerializer, $hubClientInformationSerializer,
            $clientSessionInformationSerializer, $orderContentSerializer, $logControl, $hubSettings);
    }
    
    
    /**
     * Creates a HubSessionsApiClient instance.
     *
     * @param string        $shopUrl Shop url with trailing slash.
     * @param \LanguageCode $languageCode
     *
     * @return bool|\HubSessionsApiClient
     */
    public function startSession($shopUrl, LanguageCode $languageCode)
    {
        if (!is_string($shopUrl)) {
            throw new InvalidArgumentException('Shop url argument is not a string: ' . gettype($shopUrl));
        }
        
        // start a new gambio hub session
        $hubServiceFactory         = MainFactory::create('HubServiceFactory');
        $hubSessionKeyService      = $hubServiceFactory->createHubSessionKeyService();
        $hubClientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');
        $curlRequest               = new CurlRequest();
        $logControl                = LogControl::get_instance();
        $hubSettings               = MainFactory::create('HubSettings', gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        /** @var HubSessionsApiClient $hubSessionsApiClient */
        $hubSessionsApiClient = MainFactory::create('HubSessionsApiClient', MODULE_PAYMENT_GAMBIO_HUB_URL,
            $hubSessionKeyService, $hubClientKeyConfiguration, $curlRequest, $logControl, $hubSettings);
        
        $authHash = AuthHashCreator::create();
        
        try {
            $hubSessionsApiClient->startSession($authHash, $shopUrl, $languageCode);
        } catch (UnexpectedValueException $e) {
            AuthHashCreator::invalidate($authHash);
        } catch (CurlRequestException $e) {
            AuthHashCreator::invalidate($authHash);
        }
    }
    
    
    /**
     * Returns a CartContent instance based on the global 'order' information.
     *
     * @param order $order Contains the order information.
     *
     * @return \HubPublic\ValueObjects\CartContent
     */
    public function getCartContent(order $order)
    {
        $cartItemCollection = new CartItemCollection();
        
        foreach ($order->products as $product) {
            $attributes = [];
            
            $sql        = 'SELECT `products_ean` FROM `products` WHERE `products_id` = ' . (int)$product['id'];
            $query      = xtc_db_query($sql);
            $productEan = (string)xtc_db_fetch_array($query)['products_ean'];
            $reference  = (string)$product['id'];
            $model      = (string)$product['model'];
            
            // Parse Product Properties
            if (strpos($product['id'], 'x') !== false) {
                $productsIdParts = explode('x', $product['id']);
                $combinationId   = array_pop($productsIdParts);
                $attributes      = $this->getAttributesForPropertiesCombi($combinationId, $_SESSION['languages_id']);
                $combiModel      = $this->getCombiModel($combinationId);
                $model           = empty($model) ? $combiModel : "{$model}-{$combiModel}";
            }
            
            // Parse Product Attributes
            if (strpos($product['id'], '{') !== false) {
                $exploded = explode('{', $product['id']);
                
                foreach ($exploded as $segment) {
                    if (strpos($segment, '}') === false) {
                        continue;
                    }
                    
                    $productOptionsIds = explode('}', $segment);
                    
                    $columExists     = xtc_db_query('DESCRIBE `products_attributes` "gm_ean"');
                    $attributeEanSql = '';
                    if (xtc_db_num_rows($columExists)) {
                        $attributeEanSql = '`products_attributes`.`gm_ean`,';
                    }
                    
                    $sql = '
                        SELECT
                            `products_options`.`products_options_name`,
                            `products_options_values`.`products_options_values_name`,
                            :attribute_ean_sql
                            `products_attributes`.`attributes_model`
                        FROM
                            `products_attributes`
                        INNER JOIN
                            `products_options`
                            ON `products_options`.`products_options_id` = `products_attributes`.`options_id`
                                AND `products_options`.`products_options_id` = :options_id
                                AND `products_options`.`language_id` = :language_id
                        INNER JOIN
                            `products_options_values`
                            ON `products_options_values`.`products_options_values_id` = :options_values_id
                                AND `products_options_values`.`language_id` = :language_id
                        WHERE
                            `products_attributes`.`products_id` = :products_id
                            AND `products_attributes`.`options_id` = :options_id
                            AND `products_attributes`.`options_values_id` = :options_values_id
					';
                    $sql = strtr($sql, [
                        ':attribute_ean_sql' => $attributeEanSql,
                        ':options_id'        => (int)$productOptionsIds[0],
                        ':options_values_id' => (int)$productOptionsIds[1],
                        ':language_id'       => (int)$_SESSION['languages_id'],
                        ':products_id'       => (int)$product['id'],
                    ]);
                    
                    $query          = xtc_db_query($sql);
                    $productOptions = [];
                    
                    while ($row = xtc_db_fetch_array($query)) {
                        $productOptions[] = $row;
                        if (!empty($row['attributes_model'])) {
                            $model = empty($model) ? $row['attributes_model'] : "{$model}-{$row['attributes_model']}";
                        }
                    }
                    
                    foreach ($productOptions as $productOption) {
                        if (!empty($productOption['gm_ean'])) // Replace the product EAN number if there was one in attributes.
                        {
                            $productEan = (string)$productOption['gm_ean'];
                        }
                        $attributes[(string)$productOption['products_options_name']] = (string)$productOption['products_options_values_name'];
                    }
                }
            }
            
            $productsItemCodes = $this->getProductsItemCodes((int)$product['id']);
            $imageUrl          = $this->getProductImageUrl((int)$product['id']);
            $categoryPath      = $this->getProductCategoryPath((int)$product['id'], $_SESSION['languages_id']);
            $mpn               = $productsItemCodes['code_mpn'];
            $productUrl        = $this->getProductUrl($product['id'], $product['name']);
            $quantityUnit      = (string)$product['unit_name'];
            $type              = (string)$product['product_type'];
            $brand             = $productsItemCodes['brand_name'];
            $tax               = (float)$product['tax'];
            // Prepare final cart item attributes array.
            $cartItem = new CartItem($productEan, (string)$product['name'], (float)$product['final_price'],
                (float)$product['qty'], $attributes, $imageUrl, $categoryPath, $mpn, $productUrl, $quantityUnit,
                $reference, $type, $brand, $tax, $model);
            $cartItemCollection->add($cartItem);
        }
        
        $globalsOrder     = clone $GLOBALS['order'];
        $GLOBALS['order'] = new order();
        $orderTotal       = new order_total();
        $orderTotalArray  = $orderTotal->process();
        $shippingCost     = (float)$GLOBALS['order']->info['shipping_cost'];
        $GLOBALS['order'] = $globalsOrder;
        
        $orderTotalPrice         = 0;
        $cartTotalItemCollection = new CartTotalItemCollection();
        foreach ($orderTotalArray as $module) {
            $GLOBALS[$module['code']]->output = [];
            if ($module['code'] === 'ot_total') {
                $orderTotalPrice = (float)$module['value'];
            }
            $cartTotalItem = new CartTotalItem($module['code'], strip_tags($module['title']), (float)$module['value'],
                (float)$module['changes']['total'], (float)$module['changes']['tax'],
                (float)$module['changes']['shipping_cost']);
            $cartTotalItemCollection->add($cartTotalItem);
        }
        
        $cartContent = new CartContent($cartItemCollection, $orderTotalPrice, $shippingCost, $cartTotalItemCollection);
        
        return $cartContent;
    }
    
    
    /**
     * Returns an array of product item codes; currently only code_mpn and brand_name.
     *
     * @param $productId
     *
     * @return array
     */
    protected function getProductsItemCodes($productId)
    {
        $productItemCodesSql     = '
			SELECT
				`code_mpn`, `brand_name`
			FROM
				`products_item_codes`
			WHERE
				`products_id` = :products_id
		';
        $productItemCodesSql     = strtr($productItemCodesSql, [':products_id' => (int)$productId]);
        $productsItemCodesResult = xtc_db_query($productItemCodesSql);
        $productItemCodes        = ['code_mpn' => '', 'brand_name' => ''];
        while ($productItemCodesRow = xtc_db_fetch_array($productsItemCodesResult)) {
            $productItemCodesRow['code_mpn']   = (string)$productItemCodesRow['code_mpn'];
            $productItemCodesRow['brand_name'] = (string)$productItemCodesRow['brand_name'];
            $productItemCodes                  = array_merge($productItemCodes, $productItemCodesRow);
        }
        
        return $productItemCodes;
    }
    
    
    /**
     * Returns category path for a product w/ names in a specific language.
     *
     * Format is 'Category>Subcategory>Subsubcategory'.
     *
     * @param $productId
     * @param $languageId
     *
     * @return string
     */
    protected function getProductCategoryPath($productId, $languageId)
    {
        // get product category
        $productCategorySql    = '
			SELECT
				`categories_id`
			FROM
				`products_to_categories`
			WHERE
				`products_id` = :products_id
		';
        $productCategorySql    = strtr($productCategorySql, [':products_id' => (int)$productId]);
        $productCategoryResult = xtc_db_query($productCategorySql);
        $productCategories     = [];
        while ($productCategoryRow = xtc_db_fetch_array($productCategoryResult)) {
            $productCategories[] = $productCategoryRow['categories_id'];
        }
        do {
            $productCategoryId = array_pop($productCategories);
        } while ((int)$productCategoryId === 0 && count($productCategories) > 0);
        
        // iterate upward through categories hierarchy
        $categoryPathParts = [];
        $categoryId        = (int)$productCategoryId;
        while ($categoryId > 0) {
            $categorySql    = '
				SELECT
					`cd`.`categories_name`,
					`c`.`parent_id`
				FROM
					`categories` `c`
				JOIN
					`categories_description` `cd`
					ON
						`cd`.`categories_id` = `c`.`categories_id` AND
						`cd`.`language_id` = :language_id
				WHERE
					`c`.`categories_id` = :categories_id
			';
            $categorySql    = strtr($categorySql,
                [':categories_id' => (int)$categoryId, ':language_id' => (int)$languageId]);
            $categoryResult = xtc_db_query($categorySql);
            $categoryRow    = xtc_db_fetch_array($categoryResult);
            if (empty($categoryRow)) {
                $categoryId = 0;
            } else {
                $categoryId          = $categoryRow['parent_id'];
                $categoryPathParts[] = $categoryRow['categories_name'];
            }
        }
        $categoryPathParts = array_reverse($categoryPathParts);
        $categoryPath      = implode('>', $categoryPathParts);
        
        return $categoryPath;
    }
    
    
    /**
     * Returns URL for a product’s primary image.
     *
     * @param $productId
     *
     * @return string
     */
    protected function getProductImageUrl($productId)
    {
        $productSql      = '
				SELECT
					`products_image`
				FROM
					`products`
				WHERE
					`products_id` = :products_id
			';
        $productSql      = strtr($productSql, [':products_id' => (int)$productId]);
        $productResult   = xtc_db_query($productSql);
        $productImageUrl = '';
        while ($productRow = xtc_db_fetch_array($productResult)) {
            $productImageUrl = GM_HTTP_SERVER . '/' . DIR_WS_INFO_IMAGES . $productRow['products_image'];
        }
        
        return $productImageUrl;
    }
    
    
    /**
     * Returns URL for product page.
     *
     * Uses SEOBoost if configured.
     *
     * @param $productId
     * @param $productName
     *
     * @return string
     */
    protected function getProductUrl($productId, $productName)
    {
        $gmSEOBoost = MainFactory::create('GMSEOBoost');
        if ($gmSEOBoost->boost_products === true) {
            $productUrl = xtc_href_link($gmSEOBoost->get_boosted_product_url($productId, $productName));
        } else {
            $productUrl = xtc_href_link('product_info.php', xtc_product_link($productId, $productName));
        }
        
        return $productUrl;
    }
    
    
    /**
     * Returns an array properties_name => values_name for a given combination ID and language.
     *
     * @param int $combiId
     * @param int $languageId
     *
     * @return array
     */
    protected function getAttributesForPropertiesCombi($combiId, $languageId)
    {
        $attributes = [];
        $sql        = '
            SELECT
                `properties_name`, `values_name`
            FROM
                `products_properties_index`
            WHERE
                `products_properties_combis_id` = :combis_id AND
                `language_id` = :language_id';
        $sql        = strtr($sql, [
            ':combis_id'   => (int)$combiId,
            ':language_id' => (int)$languageId,
        ]);
        $result     = xtc_db_query($sql);
        while ($row = xtc_db_fetch_array($result)) {
            $attributes[$row['properties_name']] = $row['values_name'];
        }
        
        return $attributes;
    }
    
    
    /**
     * Returns combi_model for a product variant identified by $combiId.
     *
     * @param $combiId
     *
     * @return mixed|string
     */
    protected function getCombiModel($combiId)
    {
        $combiModel = '';
        $sql        = "SELECT `combi_model` FROM `products_properties_combis` WHERE `products_properties_combis_id` = :combis_id";
        $sql        = strtr($sql, [':combis_id' => (int)$combiId]);
        $result     = xtc_db_query($sql);
        while ($row = xtc_db_fetch_array($result)) {
            $combiModel = $row['combi_model'];
        }
        
        return $combiModel;
    }
    
    
    /**
     * Returns a CustomerInformation instance based on the global 'order' information.
     *
     * @param order $order Contains the order information.
     *
     * @return \HubPublic\ValueObjects\CustomerInformation
     */
    public function getCustomerInformation(order $order)
    {
        $customer  = $this->getCustomer($_SESSION['customer_id']);
        $b2bStatus = '0';
        if (isset($_SESSION['customer_b2b_status']) && (bool)$_SESSION['customer_b2b_status'] === true) {
            $b2bStatus = '1';
        }
        
        $customerInformationBuilder = new CustomerInformationBuilder();
        $customerInformationBuilder->setCustomerNumber((string)$order->customer['csID'])
                                   ->setCustomerFirstName((string)$order->customer['firstname'])
                                   ->setCustomerLastName((string)$order->customer['lastname'])
                                   ->setCustomerGender((string)$order->customer['gender'])
                                   ->setCustomerCompany((string)$order->customer['company'])
                                   ->setCustomerAddress1(trim((string)$order->customer['street_address'] . ' '
                                                              . (string)$order->customer['house_number']))
                                   ->setCustomerAddress2((string)$order->customer['additional_address_info'])
                                   ->setCustomerPostalCode((string)$order->customer['postcode'])
                                   ->setCustomerCity((string)$order->customer['city'])
                                   ->setCustomerState((string)$order->customer['state'])
                                   ->setCustomerCountry((string)$order->customer['country']['title'])
                                   ->setCustomerCountryCode((string)$order->customer['country']['iso_code_2'])
                                   ->setCustomerDateOfBirth((string)$customer['customers_dob'])
                                   ->setCustomerPhone((string)$order->customer['telephone'])
                                   ->setCustomerFax((string)$customer['customers_fax'])
                                   ->setCustomerEmail((string)$customer['customers_email_address'])
                                   ->setCustomerB2bStatus((string)$b2bStatus)
                                   ->setBillingFirstName((string)$order->billing['firstname'])
                                   ->setBillingLastName((string)$order->billing['lastname'])
                                   ->setBillingGender((string)$order->billing['gender'])
                                   ->setBillingCompany((string)$order->billing['company'])
                                   ->setBillingAddress1(trim((string)$order->billing['street_address'] . ' '
                                                             . (string)$order->billing['house_number']))
                                   ->setBillingAddress2((string)$order->billing['additional_address_info'])
                                   ->setBillingPostalCode((string)$order->billing['postcode'])
                                   ->setBillingCity((string)$order->billing['city'])
                                   ->setBillingState((string)$order->billing['state'])
                                   ->setBillingCountry((string)$order->billing['country']['title'])
                                   ->setBillingCountryCode((string)$order->billing['country']['iso_code_2'])
                                   ->setShippingFirstName((string)$order->delivery['firstname'])
                                   ->setShippingLastName((string)$order->delivery['lastname'])
                                   ->setShippingGender((string)$order->delivery['gender'])
                                   ->setShippingCompany((string)$order->delivery['company'])
                                   ->setShippingAddress1(trim((string)$order->delivery['street_address'] . ' '
                                                              . (string)$order->delivery['house_number']))
                                   ->setShippingAddress2((string)$order->delivery['additional_address_info'])
                                   ->setShippingPostalCode((string)$order->delivery['postcode'])
                                   ->setShippingCity((string)$order->delivery['city'])
                                   ->setShippingState((string)$order->delivery['state'])
                                   ->setShippingCountry((string)$order->delivery['country']['title'])
                                   ->setShippingCountryCode((string)$order->delivery['country']['iso_code_2']);
        
        return $customerInformationBuilder->build();
    }
    
    
    /**
     * Returns a HubClientInformation instance
     *
     * @return \HubPublic\ValueObjects\HubClientInformation
     */
    public function getHubClientInformation()
    {
        $hubClientKeyConfiguration = MainFactory::create('HubClientKeyConfiguration');
        $hubClientKey              = new HubClientKey($hubClientKeyConfiguration->get());
        $hubClientInformation      = new HubClientInformation($hubClientKey,
            ltrim(gm_get_conf('INSTALLED_VERSION'), 'v'), HTTP_SERVER . DIR_WS_CATALOG);
        
        return $hubClientInformation;
    }
    
    
    /**
     * Returns a ClientSessionInformation instance.
     *
     * @param order $order Contains the order information.
     *
     * @return \HubPublic\ValueObjects\ClientSessionInformation
     */
    public function getClientSessionInformation(order $order)
    {
        $clientSessionInformation = new ClientSessionInformation(new HubSessionKey($_SESSION['gambio_hub_session_key']
                                                                                   ?? ''),
            $_SESSION['language_code'] ?? '', (string)$order->info['currency'], $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? '');
        
        return $clientSessionInformation;
    }
    
    
    /**
     * Get customer information from database.
     *
     * @param int $customerId The customer's row ID.
     *
     * @return array Returns an array with the customer's data.
     */
    public function getCustomer($customerId)
    {
        $sql   = 'SELECT * FROM `customers` WHERE `customers_id` = ' . (int)$customerId;
        $query = xtc_db_query($sql);
        
        return xtc_db_fetch_array($query);
	}
}
