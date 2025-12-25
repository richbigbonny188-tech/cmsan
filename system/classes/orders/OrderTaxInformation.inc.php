<?php
/* --------------------------------------------------------------
   OrderTaxInformation.inc.php 2023-05-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderTaxInformations
 */
class OrderTaxInformation
{
    /**
     * @var TaxItem
     */
    protected $taxItem;
    
    /**
     * @var TaxItemWriter
     */
    protected $taxItemWriter;
    
    /**
     * @var TaxItemReader
     */
    protected        $taxItemReader;
    
    protected static $tableOrders      = 'orders';
        
    protected static $tableTaxClass    = 'tax_class';
    
    protected static $tableTaxRate     = 'tax_rates';
    
    
    /** @var int */
    protected $orderId = 0;
    
    
    /**
     *
     */
    public function __construct()
    {
        $this->taxItem       = MainFactory::create_object('TaxItem', []);
        $this->taxItemWriter = MainFactory::create_object('TaxItemWriter', [$this->taxItem]);
        $this->taxItemReader = MainFactory::create_object('TaxItemReader', []);
    }
    
    
    /**
     * @param $p_orderId
     */
    public function saveUnsavedTaxInformation($p_orderId)
    {
        $this->orderId = (int)$p_orderId;
        
        $isSavedBefore = $this->taxItemReader->orderIdIsSaved($this->orderId);
        
        if ($isSavedBefore === false) {
            $this->saveTaxInformation($p_orderId);
        }
    }
    
    
    /**
     * @param int $p_orderId
     */
    public function saveTaxInformation($p_orderId)
    {
        $this->orderId = (int)$p_orderId;
        
        /* Get Order information */
        
        $query = "SELECT
					orders_total.`title`,
					orders_total.`value`,
                    geo_zones.`geo_zone_name` AS geo_zone
					FROM orders_total
                    LEFT JOIN `orders` on orders_total.orders_id = orders.orders_id
                         LEFT JOIN `countries` on orders.delivery_country_iso_code_2 = countries.countries_iso_code_2
                         INNER JOIN (SELECT zone_country_id, MAX(geo_zone_id) AS geo_zone_id
                                     FROM zones_to_geo_zones
                                     GROUP BY zone_country_id) zones
                                    ON countries.countries_id = zones.zone_country_id
                         LEFT JOIN `geo_zones` on zones.geo_zone_id = geo_zones.geo_zone_id
					WHERE
						orders_total.`class` = 'ot_tax' AND orders_total.`orders_id` = '" . strval($this->orderId) . "'";
        
        $result = xtc_db_query($query);
        
        $taxesInfoArray = [];
        while ($taxBasicInformation = xtc_db_fetch_array($result)) {
            $taxesInfoArray[] = [
                'title'    => $taxBasicInformation['title'],
                'value'    => $taxBasicInformation['value'],
                'geo_zone' => $taxBasicInformation['geo_zone'],
            ];
        }
        
        foreach ($taxesInfoArray as $taxInfoArray) {
            if (is_array($taxInfoArray)) {
                $taxItem = $this->_prepareTaxInfoDataset($taxInfoArray);
                $this->taxItemWriter->insertDB($taxItem);
            }
        }
    }
    
    
    /**
     * @param array $taxInfoArray
     *
     * @return \TaxItem
     */
    protected function _prepareTaxInfoDataset(array $taxInfoArray = null)
    {
        $taxItem = clone $this->taxItem;
        
        $taxItem->setLastChangeDatetime(new DateTime());
        
        if (is_array($taxInfoArray)) {
            $title = $this->_getTaxTitle($taxInfoArray['title']);
            $taxItem->setTaxDescription($title);
            $taxItem->setTax($taxInfoArray['value']);
            
            $additionalTaxInfo = $this->_getAdditionalTaxInfo($title, $taxInfoArray['geo_zone']);
            
            $taxItem->setTaxClass($additionalTaxInfo->getTaxClass());
            $taxItem->setTaxRate($additionalTaxInfo->getTaxRate());
            $taxItem->setTaxZone($additionalTaxInfo->getTaxZone());
            $taxItem->setCurrency($additionalTaxInfo->getCurrency());
            $taxItem->setOrderId($this->orderId);
            
            /* Get gross and net */
            
            $taxRate = $taxItem->getTaxRate();
            $tax     = $taxItem->getTax();
            
            if ($taxRate > 0) {
                $net   = ($tax / $taxRate) * 100;
                $net   = round($net, 4);
                $gross = $net + $tax;
                
                $taxItem->setNet($net);
                $taxItem->setGross($gross);
            }
            
            return $taxItem;
        }
    }
    
    
    /**
     * @param string $p_taxDescription
     * @param string $p_geoZone
     *
     * @return TaxItem
     */
    protected function _getAdditionalTaxInfo($p_taxDescription, $p_geoZone)
    {
        $taxItem = clone $this->taxItem;
        
        $taxClass = $this->_getTaxClass($p_taxDescription);
        $taxZone  = $this->_getTaxZone($p_geoZone);
        $taxRate  = $this->_getTaxRate($p_taxDescription);
        $currency = $this->_getCurrency();
        
        $taxItem->setTaxClass($taxClass);
        $taxItem->setTaxZone($taxZone);
        $taxItem->setCurrency($currency);
        $taxItem->setTaxRate($taxRate);
        
        return $taxItem;
    }
    
    
    /**
     * @param string $p_taxDescription
     *
     * @return string
     */
    protected function _getTaxClass($p_taxDescription)
    {
        $where   = 'tax_description = \'' . xtc_db_input($p_taxDescription) . '\'';
        $taxInfo = $this->_getOneDataset(self::$tableTaxRate, $where);
        
        $taxClassId = $taxInfo['tax_class_id'] ?? 0;
        
        $where   = 'tax_class_id = ' . (int)$taxClassId;
        $taxInfo = $this->_getOneDataset(self::$tableTaxClass, $where);
        
        $taxClass = $taxInfo['tax_class_title'] ?? '';
        
        return $taxClass;
    }
    
    
    /**
     * @param $p_taxDescription
     *
     * @return mixed
     */
    protected function _getTaxRate($p_taxDescription)
    {
        $where   = 'tax_description = \'' . xtc_db_input($p_taxDescription) . '\'';
        $taxInfo = $this->_getOneDataset(self::$tableTaxRate, $where);
        
        $taxRate = $taxInfo['tax_rate'] ?? 0.0;
        
        return $taxRate;
    }
    
    
    /**
     * @param $p_taxZone
     *
     * @return string
     */
    protected function _getTaxZone($p_taxZone)
    {
        return $p_taxZone;
    }
    
    
    /**
     * @return string
     */
    protected function _getCurrency()
    {
        $orderId = $this->orderId;
        
        $where   = 'orders_id = ' . (int)$orderId;
        $taxInfo = $this->_getOneDataset(self::$tableOrders, $where);
        
        $currency = $taxInfo['currency'];
        
        return $currency;
    }
    
    
    /**
     * @return DateTime
     */
    protected function _getDateOfPurchase()
    {
        $where      = 'orders_id = ' . (int)$this->orderId;
        $orderArray = $this->_getOneDataset(self::$tableOrders, $where);
        
        $dateOfPurchase = new EmptyDateTime($orderArray['date_purchased']);
        
        return $dateOfPurchase;
    }
    
    
    /**
     * @param string $tablename
     * @param string $where
     *
     * @return array
     */
    protected function _getOneDataset($tablename, $where)
    {
        $query = "SELECT * FROM `%s` WHERE %s";
        $query = sprintf($query, $tablename, $where);
        
        $result = xtc_db_query($query);
        
        $oneDataset = xtc_db_fetch_array($result);
        
        return $oneDataset;
    }
    
    
    /**
     * @param string $taxTitle
     *
     * @return string
     */
    protected function _getTaxTitle($taxTitle)
    {
        $title                 = $taxTitle;
        $taxRateStringPosition = 6;
        
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] === '1' && defined('TAX_ADD_TAX')) {
            $taxRateStringPosition = strlen(TAX_ADD_TAX);
        } elseif (defined('TAX_NO_TAX')) {
            $taxRateStringPosition = strlen(TAX_NO_TAX);
        }
        
        $title = substr($title, $taxRateStringPosition); // remove i.e. "inkl. "
        $title = substr($title, 0, -1); // remove ":"
        
        if (isset($_SESSION['customer_cart_tax_info'])
            && array_key_exists($title,
                                $_SESSION['customer_cart_tax_info'])) {
            $title = xtc_get_tax_description($_SESSION['customer_cart_tax_info'][$title]['tax_class_id'],
                                             $_SESSION['customer_cart_tax_info'][$title]['country_id'],
                                             $_SESSION['customer_cart_tax_info'][$title]['zone_id'],
                                             -1,
                                             true);
        }
        
        return $title;
    }
}
