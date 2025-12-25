<?php

/* --------------------------------------------------------------
   QuickEditProductPropertiesWriter.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductPropertiesWriter
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Repositories
 */
class QuickEditProductPropertiesWriter implements QuickEditProductPropertiesWriterInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var int
     */
    protected $combiId;
    
    /**
     * @var float
     */
    protected $taxRate;
    
    /**
     * @var QuickEditPropertiesOverviewColumns
     */
    protected $quickEditPropertiesOverviewColumns;
    
    
    /**
     * QuickEditProductPropertiesWriter constructor.
     *
     * @param CI_DB_query_builder                $db                                  Database query builder instance.
     * @param QuickEditPropertiesOverviewColumns $quickEditPropertiesOverviewColumns  QuickEdit properties overview
     *                                                                                columns.
     */
    public function __construct($db, QuickEditPropertiesOverviewColumns $quickEditPropertiesOverviewColumns)
    {
        $this->db                                 = $db;
        $this->quickEditPropertiesOverviewColumns = $quickEditPropertiesOverviewColumns;
    }
    
    
    /**
     * Saves product by product-combi ID.
     *
     * @param array $productCombi Contains product data to be saved.
     *
     * @return bool Returns the operation result.
     */
    public function setByCombisId(array $productCombi)
    {
        try {
            foreach ($productCombi as $combiId => $items) {
                $this->combiId = $combiId;
                
                if (PRICE_IS_BRUTTO === 'true') {
                    $this->_setTaxRate();
                }
                
                $this->db->update('products_properties_combis',
                                  $this->_collectUpdateData($items),
                                  ['products_properties_combis_id' => $this->combiId]);
            }
            
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
    
    
    /**
     * Sets the tax rate of a product.
     */
    protected function _setTaxRate()
    {
        $result = $this->db->select('tax_rate')
            ->join('products_properties_combis',
                   'products_properties_combis.products_properties_combis_id = ' . $this->combiId)
            ->join('tax_rates', 'tax_rates.tax_class_id = products.products_tax_class_id')
            ->join('zones_to_geo_zones',
                   'zones_to_geo_zones.zone_country_id = ' . (int)STORE_COUNTRY)
            ->where('tax_rates.tax_zone_id = zones_to_geo_zones.geo_zone_id')
            ->where('products.products_id = products_properties_combis.products_id')
            ->get('products')
            ->row();
        
        $this->taxRate = $result->tax_rate;
    }
    
    
    /**
     * Returns the updated data of a product.
     *
     * @param array $items Items.
     *
     * @return array Returns the updated data of a product is an array.
     */
    protected function _collectUpdateData(array $items)
    {
        $updateData = [];
        
        $propertiesOverviewColumns = $this->_editableColumns();
        
        foreach ($items as $key => $value) {
            if ($key === 'combiPrice') {
                $value = $this->_calculatePrice($value, $propertiesOverviewColumns[$key]);
            } elseif ($key === 'combiQuantity' || $key === 'combiWeight') {
                $value = $this->_calculateUnit($value, $propertiesOverviewColumns[$key]);
            }
            
            $updateData[$propertiesOverviewColumns[$key]] = $value;
        }
        
        return $updateData;
    }
    
    
    /**
     * Calculate the price of the value.
     *
     * @param string $value  Value.
     * @param string $column Column of the table.
     *
     * @return float|int Price of the product.
     */
    protected function _calculatePrice($value, $column)
    {
        $operation = preg_replace('/[^-+%]/', '', $value);
        $value     = preg_replace('/[^.,\d]/', '', $value);
        $value     = str_replace(',', '.', $value);
        $column    = end(explode('.', $column));
        
        $query  = $this->db->select($column)->where('products_properties_combis_id', $this->combiId);
        $result = $query->get('products_properties_combis')->row_array();
        
        if (strpos($operation, '-') !== false) {
            return strpos($operation, '%') !== false ? $result[$column] * (1 - $value / 100) : $result[$column] - $value
                                                                                                                  / (1
                                                                                                                     + $this->taxRate
                                                                                                                       / 100);
        }
        
        if (strpos($operation, '+') !== false) {
            return strpos($operation, '%') !== false ? $result[$column] * (1 + $value / 100) : $result[$column] + $value
                                                                                                                  / (1
                                                                                                                     + $this->taxRate
                                                                                                                       / 100);
        }
        
        if (strpos($operation, '%') !== false) {
            return $result[$column] * (1 - $value / 100);
        }
        
        return $value / (1 + $this->taxRate / 100);
    }
    
    
    /**
     * Calculates the unit of the value.
     *
     * @param string $value  Value.
     * @param string $column Column of the table.
     *
     * @return mixed
     */
    protected function _calculateUnit($value, $column)
    {
        $operation = preg_replace('/[^-+%]/', '', $value);
        $value     = preg_replace('/[^.,\d]/', '', $value);
        $value     = str_replace(',', '.', $value);
        $column    = end(explode('.', $column));
        
        $query  = $this->db->select($column)->where('products_properties_combis_id', $this->combiId);
        $result = $query->get('products_properties_combis')->row_array();
        
        if (strpos($operation, '-') !== false) {
            return strpos($operation, '%') !== false ? $result[$column] * (1 - $value / 100) : $result[$column]
                                                                                               - $value;
        }
        
        if (strpos($operation, '+') !== false) {
            return strpos($operation, '%') !== false ? $result[$column] * (1 + $value / 100) : $result[$column]
                                                                                               + $value;
        }
        
        return $value;
    }
    
    
    /**
     * Returns the editable columns for the product.
     *
     * @return array Returns the editable columns for the product is an array.
     */
    protected function _editableColumns()
    {
        $propertiesOverviewColumns = $this->_delegateFilterToColumn();
        
        $editableColumns = [
            'products_properties_combis.combi_model',
            'products_properties_combis.combi_ean',
            'products_properties_combis.combi_quantity',
            'products_properties_combis.combi_price',
            'products_properties_combis.combi_price_type',
            'products_properties_combis.combi_shipping_status_id',
            'products_properties_combis.combi_weight'
        ];
        
        return array_filter($propertiesOverviewColumns,
            function ($columns) use ($editableColumns) {
                return in_array($columns, $editableColumns, true);
            });
    }
    
    
    /**
     * Returns the required columns for the overview of the properties.
     *
     * @return array Returns an array of the required columns for the overview of the properties.
     */
    protected function _delegateFilterToColumn()
    {
        $propertiesOverviewColumns = [];
        
        foreach ($this->quickEditPropertiesOverviewColumns->serializeColumns() as $columns) {
            $propertiesOverviewColumns[$columns['name']] = $columns['field'];
        }
        
        return $propertiesOverviewColumns;
    }
}