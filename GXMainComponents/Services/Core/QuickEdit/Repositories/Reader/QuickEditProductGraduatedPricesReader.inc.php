<?php

/* --------------------------------------------------------------
   QuickEditProductGraduatedPricesReader.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductGraduatedPricesReader
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Repositories
 */
class QuickEditProductGraduatedPricesReader implements QuickEditProductGraduatedPricesReaderInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * QuickEditProductGraduatedPricesReader constructor.
     *
     * @param CI_DB_query_builder $db Database query builder instance.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Returns the graduated prices of a product or an empty array nothing was found.
     *
     * @param array|null $productIds Array containing the selected product IDs to be processed.
     *
     * @return array Returns array that contains the graduated prices information.
     */
    public function getGraduatedPrices(array $productIds = null)
    {
        if (empty($productIds)) {
            return [];
        }
        
        $taxRate = 0;
        $result  = [];
        
        foreach ($productIds as $productId) {
            $personalOffers = $this->_getPersonalOffersByProductId($productId);
            
            if (PRICE_IS_BRUTTO === 'true') {
                $taxRate = $this->_getTaxRateByProductId($productId);
            }
            
            $result[$productId]['customer'] = array_map(function ($value) use ($taxRate) {
                $value['graduations'] = array_map(function ($item) use ($taxRate) {
                    $item['quantity']       = (new DecimalType($item['quantity']))->asDecimal();
                    $item['personal_offer'] = sprintf('%01.2f',
                                                      round($item['personal_offer'] * (1 + $taxRate / 100), 2));
                    
                    return $item;
                },
                    $value['graduations']);
                
                return $value;
            },
                $personalOffers);
        }
        
        return $result;
    }
    
    
    /**
     * Provides customer-specific product offers.
     *
     * @param int|null $productId Optional, product ID to be processed.
     *
     * @return array Returns the graduated prices or an empty array if nothing was found.
     */
    protected function _getPersonalOffersByProductId($productId = null)
    {
        $result    = [];
        $customers = $this->_getCustomerStatuses();
        
        foreach ($customers as $customer) {
            $customerStatusId                         = $customer['customers_status_id'];
            $result[$customerStatusId]['graduations'] = $this->db->where('products_id', $productId)
                ->order_by('products_id',
                           'asc')
                ->order_by('quantity', 'asc')
                ->get('personal_offers_by_customers_status_' . $customerStatusId)
                ->result_array();
            $result[$customerStatusId]['status_id']   = $customer['customers_status_id'];
            $result[$customerStatusId]['status_name'] = $customer['customers_status_name'];
        }
        
        return $result;
    }
    
    
    /**
     * Returns the names of the existing customer groups.
     *
     * @return array Returns the customer status info or an empty array if nothing was found.
     */
    protected function _getCustomerStatuses()
    {
        $customers = $this->db->select(['customers_status_id', 'customers_status_name'])
            ->where('language_id',
                    $_SESSION['languages_id'])
            ->where_not_in('customers_status_id', [0])
            ->get('customers_status')
            ->result_array();
        
        return $customers;
    }
    
    
    /**
     * Returns the tax rate for the specified product.
     *
     * @param int $productId Product ID to be processed.
     *
     * @return array Returns an object that contains the product tax rate.
     */
    protected function _getTaxRateByProductId($productId)
    {
        $result = $this->db->select('tax_rate')
            ->join('tax_rates',
                   'tax_rates.tax_class_id = products.products_tax_class_id')
            ->join('zones_to_geo_zones', 'zones_to_geo_zones.zone_country_id = ' . (int)STORE_COUNTRY)
            ->where('products.products_id',
                    $productId)
            ->where('tax_rates.tax_zone_id = zones_to_geo_zones.geo_zone_id')
            ->get('products')
            ->row();
        
        return $result->tax_rate;
    }
}