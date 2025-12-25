<?php
/* --------------------------------------------------------------
   QuickEditProductSpecialPricesWriter.inc.php 2020-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductSpecialPricesWriter
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Repositories
 */
class QuickEditProductSpecialPricesWriter implements QuickEditProductSpecialPricesWriterInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var array
     */
    protected $productIds;
    
    /**
     * @var float
     */
    protected $taxRate;
    
    /**
     * @var QuickEditSpecialPricesOverviewColumns
     */
    protected $quickEditSpecialPricesOverviewColumns;
    
    
    /**
     * QuickEditProductsSpecialPriceWriter constructor.
     *
     * @param CI_DB_query_builder                   $db                                    Database query builder
     *                                                                                     instance.
     * @param QuickEditSpecialPricesOverviewColumns $quickEditSpecialPricesOverviewColumns QuickEdit properties overview
     *                                                                                     columns.
     */
    public function __construct(
        CI_DB_query_builder $db,
        QuickEditSpecialPricesOverviewColumns $quickEditSpecialPricesOverviewColumns
    ) {
        $this->db                                    = $db;
        $this->quickEditSpecialPricesOverviewColumns = $quickEditSpecialPricesOverviewColumns;
    }
    
    
    /**
     * Saves the changed data regarding the special price.
     *
     * @param array $specialPrice Contains the special prices data.
     *
     * @return bool Returns the operation result.
     */
    public function setSpecialPriceById(array $specialPrice)
    {
        try {
            foreach ($specialPrice as $productIds => $productId) {
                $this->productIds = $productIds;
                
                if (PRICE_IS_BRUTTO === 'true') {
                    $this->_setTaxRate();
                }
                
                $result = $this->db->where('products_id', $this->productIds)->get('specials')->num_rows();
                
                if ($result === 0) {
                    $specialPrice                         = $this->_collectUpdateData($productId);
                    $specialPrice['specials.products_id'] = $this->productIds;
                    
                    if (!array_key_exists('specials.expires_date', $specialPrice)) {
                        $specialPrice['specials.expires_date'] = date('Y-m-d', strtotime('+7 days'));
                    }
                    
                    $this->db->insert('specials', $specialPrice);
                    
                    continue;
                }
                
                $this->db->update('specials',
                                  $this->_collectUpdateData($productId),
                                  ['products_id' => $this->productIds]);
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
            ->join('tax_rates',
                   'tax_rates.tax_class_id = products.products_tax_class_id')
            ->join('zones_to_geo_zones', 'zones_to_geo_zones.zone_country_id = ' . (int)STORE_COUNTRY)
            ->where('products.products_id',
                    $this->productIds)
            ->where('tax_rates.tax_zone_id = zones_to_geo_zones.geo_zone_id')
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
        
        $specialsOverviewColumns = $this->_editableColumns();
        
        foreach ($items as $key => $value) {
            if ($key === 'specialPrice') {
                $value = $this->_calculatePrice($value, $specialsOverviewColumns[$key]);
            } elseif ($key === 'specialPriceQuantity') {
                $value = $this->_calculateUnit($value, $specialsOverviewColumns[$key]);
            } elseif ($key === 'specialPriceExpiresDate') {
                $value = $this->_setSpecialPriceExpirationDate($value);
            }
            
            $updateData[$specialsOverviewColumns[$key]] = $value;
        }
        
        return $updateData;
    }
    
    
    /**
     * Calculate the price of the value.
     *
     * @param string $value  Value.
     * @param string $column Column of the table.
     *
     * @return float|int Returns the calculated unit of the price.
     */
    protected function _calculatePrice($value, $column)
    {
        $operation = preg_replace('/[^-+%]/', '', $value);
        $value     = preg_replace('/[^.,\d]/', '', $value);
        $value     = str_replace(',', '.', $value);
        $column    = end(explode('.', $column));
        
        $query  = $this->db->select($column)->where('products_id', $this->productIds);
        $result = $query->get('specials')->row_array();
        
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
            if ($column === 'specials_new_products_price') {
                if (empty($this->price)) {
                    $productsPrice = $query->select('products.products_id, products.products_price')
                        ->where('products.products_id',
                                $this->productIds)
                        ->get('products')
                        ->row();
                    
                    return $productsPrice->products_price * (1 - $value / 100);
                }
                
                return $this->price * (1 - $value / 100);
            }
            
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
     * @return mixed Returns the calculated unit of the value.
     */
    protected function _calculateUnit($value, $column)
    {
        $operation = preg_replace('/[^-+%]/', '', $value);
        $value     = preg_replace('/[^.,\d]/', '', $value);
        $value     = str_replace(',', '.', $value);
        $column    = end(explode('.', $column));
        
        $query  = $this->db->select($column)->where('products_id', $this->productIds);
        $result = $query->get('specials')->row_array();
        
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
     * Sets the special price expires date.
     *
     * @param string $expirationDate Expiration date string.
     *
     * @return string Returns the special price expires date in the specified format.
     */
    protected function _setSpecialPriceExpirationDate($expirationDate)
    {
        $expirationDate = str_replace(',', '.', $expirationDate);
        $expirationDate = explode('-', $expirationDate);
        $expirationDate = trim(end($expirationDate));
        $expirationDate = explode('.', $expirationDate);
        
        if (strlen($expirationDate[2]) === 2) {
            $expirationDate[2] = '20' . $expirationDate[2];
        }
        
        $expirationDate = implode('.', $expirationDate);
        $dateFormat     = ($_SESSION['language_code'] === 'de') ? 'd.m.Y' : 'm.d.Y';
        $dateValue      = DateTime::createFromFormat($dateFormat, $expirationDate);
        
        return $dateValue->format('Y-m-d');
    }
    
    
    /**
     * Returns the editable columns for the product.
     *
     * @return array Returns the editable columns for the product is an array.
     */
    protected function _editableColumns()
    {
        $specialsOverviewColumns = $this->_delegateFilterToColumn();
        
        $editableColumns = [
            'specials.specials_new_products_price',
            'specials.specials_quantity',
            'specials.expires_date',
            'specials.status',
        ];
        
        return array_filter($specialsOverviewColumns,
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
        $specialsOverviewColumns = [];
        
        foreach ($this->quickEditSpecialPricesOverviewColumns->serializeColumns() as $columns) {
            $specialsOverviewColumns[$columns['name']] = $columns['field'];
        }
        
        return $specialsOverviewColumns;
    }
}
