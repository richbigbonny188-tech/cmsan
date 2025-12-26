<?php

/* --------------------------------------------------------------
   QuickEditProductSpecialPricesReader.inc.php 2017-04-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductSpecialPricesReader
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Repositories
 */
class QuickEditProductSpecialPricesReader implements QuickEditProductSpecialPricesReaderInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var string
     */
    protected $brutto;
    
    /**
     * @var QuickEditSpecialPricesOverviewColumns
     */
    protected $quickEditSpecialPricesOverviewColumns;
    
    
    /**
     * QuickEditProductsSpecialPriceReader constructor.
     *
     * @param CI_DB_query_builder                   $db                                    Database query builder
     *                                                                                     instance.
     * @param QuickEditSpecialPricesOverviewColumns $quickEditSpecialPricesOverviewColumns QuickEdit special price
     *                                                                                     overview columns
     */
    public function __construct(
        CI_DB_query_builder $db,
        QuickEditSpecialPricesOverviewColumns $quickEditSpecialPricesOverviewColumns
    ) {
        $this->db                                    = $db;
        $this->quickEditSpecialPricesOverviewColumns = $quickEditSpecialPricesOverviewColumns;
        
        if (PRICE_IS_BRUTTO === 'true') {
            $this->brutto = ' / (1 + (`tax_rates`.`tax_rate` / 100))';
        }
    }
    
    
    /**
     * Returns the special prices of the indicated products.
     *
     * @param array $productIds       Array containing the product IDs to be processed.
     * @param array $filterParameters Contains filter parameters.
     *
     * @return array Returns the query result as a pure array, or an empty array when no result is produced.
     */
    public function getFilteredSpecialPrices(array $productIds, array $filterParameters)
    {
        $this->_join();
        $specialPriceOverviewColumns = $this->_delegateFilterToColumn();
        
        foreach ($filterParameters as $column => $value) {
            $value               = preg_replace('/[^\w\s-+<>*.,"\']/', '', $value);
            $valueWithoutLetters = preg_replace('/\D/', '', $value);
            
            if (($column === 'specialPrice' || $column === 'productsPrice') && empty($valueWithoutLetters)) {
                return [];
            }
            
            if (is_array($value)) {
                $this->_addSqlWhereGroupCondition($specialPriceOverviewColumns[$column], $value);
                
                continue;
            }
            
            if (strpos($value, '<') === 0 || strpos($value, '>') >= 1) {
                $this->_addSqlWhereLessThanCondition($specialPriceOverviewColumns[$column], $value);
                
                continue;
            }
            
            if (strpos($value, '>') === 0 || strpos($value, '<') >= 1) {
                $this->_addSqlWhereMoreThanCondition($specialPriceOverviewColumns[$column], $value);
                
                continue;
            }
            
            if (strpos($value, '*') !== false) {
                $this->_addSqlWhereLikeCondition($specialPriceOverviewColumns[$column], $value);
                
                continue;
            }
            
            $this->_addSqlWhereCondition($specialPriceOverviewColumns[$column], $value);
        }
        
        return $this->db->select($this->_columns())
            ->where_in('products.products_id', $productIds)
            ->group_by('products.products_id')
            ->get('products')
            ->result_array();
    }
    
    
    /**
     * Returns the record number of the filtered special prices.
     *
     * @param array $productIds       Array containing the product IDs to be processed.
     * @param array $filterParameters Contains filter parameters.
     *
     * @return int Returns the number of special prices found.
     */
    public function getFilteredSpecialPricesCount(array $productIds, array $filterParameters)
    {
        return count($this->getFilteredSpecialPrices($productIds, $filterParameters));
    }
    
    
    /**
     * Get special prices record count.
     *
     * @return int Returns the number of all special prices found.
     */
    public function getSpecialPricesCount()
    {
        return (int)$this->db->count_all('specials');
    }
    
    
    /**
     * Sets the starting point of the pagination and the number of products.
     *
     * @param IntType|null $start  Starting point.
     * @param IntType|null $length Number of products.
     *
     * @return QuickEditProductSpecialPricesReaderInterface Returns same instance for chained method calls.
     */
    public function paginateSpecialPrices(IntType $start = null, IntType $length = null)
    {
        $this->db->limit($length->asInt(), $start->asInt());
        
        return $this;
    }
    
    
    /**
     * Sets the sorting order of the products.
     *
     * @param StringType|null $orderBy Sorting order (ASC or DESC)
     *
     * @return QuickEditProductSpecialPricesReaderInterface Returns same instance for chained method calls.
     */
    public function sortSpecialPrices(StringType $orderBy = null)
    {
        $this->db->order_by($orderBy->asString());
        
        return $this;
    }
    
    
    /**
     * Specifies the where conditions for the database query
     *
     * @param $column Column.
     * @param $value  Value.
     */
    protected function _addSqlWhereCondition($column, $value)
    {
        if ($column === 'specials.specials_new_products_price' || $column === 'products.products_price') {
            $this->_addSqlWhereRoundCondition($column, $value);
            
            return;
        }
        
        if ($column === 'specials.expires_date') {
            $this->_addSqlWhereDateCondition($column, $value);
            
            return;
        }
        
        $this->db->where($column, $value);
    }
    
    
    /**
     * Starts and ends a group expression with 'where' condition in conjunction with a round function.
     *
     * @param $column Column.
     * @param $value  Value.
     */
    protected function _addSqlWhereRoundCondition($column, $value)
    {
        $this->db->where($column . ' = ROUND(' . $value . $this->brutto . ', 4)');
    }
    
    
    /**
     * Sets the 'where date' condition.
     *
     * @param       $column Column.
     * @param array $value  Value.
     */
    protected function _addSqlWhereDateCondition($column, $value)
    {
        $dateFormat = ($_SESSION['language_code'] === 'de') ? 'd.m.y' : 'm.d.y';
        $dateValue  = DateTime::createFromFormat($dateFormat, $value);
        
        $this->db->where($column, $dateValue->format('Y-m-d'));
    }
    
    
    /**
     * Starts and ends a group expression with 'where group' condition.
     *
     * @param       $column Column.
     * @param array $value  Value.
     */
    protected function _addSqlWhereGroupCondition($column, array $value)
    {
        if ($column === 'specials.expires_date') {
            $this->_addSqlWhereBetweenCondition($column, $value);
            
            return;
        }
        
        $this->db->group_start()->where($column, array_shift($value));
        
        foreach ($value as $item) {
            $this->db->or_where($column, $item);
        }
        
        $this->db->group_end();
    }
    
    
    /**
     * Starts and ends a group expression with 'like' condition.
     *
     * @param $column Column.
     * @param $value  Value.
     */
    protected function _addSqlWhereLikeCondition($column, $value)
    {
        $this->db->where($column . ' LIKE ', str_replace('*', '%', $value));
    }
    
    
    /**
     * Starts and ends a group expression with 'where between' condition.
     *
     * @param       $column Column.
     * @param array $value  Value.
     */
    protected function _addSqlWhereBetweenCondition($column, array $value)
    {
        $value = str_replace(['<', '>'], '', $value);
        
        if ($column === 'specials.specials_new_products_price' || $column === 'products.products_price') {
            $this->db->group_start()->where($column . ' >= ROUND(' . reset($value) . $this->brutto . ', 4)');
            $this->db->where($column . ' <= ROUND(' . end($value) . $this->brutto . ', 4)');
            $this->db->group_end();
            
            return;
        }
        
        if ($column === 'specials.expires_date') {
            $dateFormat     = ($_SESSION['language_code'] === 'de') ? 'd.m.y' : 'm.d.y';
            $dateStartValue = DateTime::createFromFormat($dateFormat, reset($value));
            $dateEndValue   = DateTime::createFromFormat($dateFormat, end($value));
            
            $this->db->where($column . ' >=', $dateStartValue->format('Y-m-d 00:00:00'));
            $this->db->where($column . ' <=', $dateEndValue->format('Y-m-d 23:59:59'));
            
            return;
        }
        
        $this->db->where($column . ' >= ' . reset($value));
        $this->db->where($column . ' <= ' . end($value));
    }
    
    
    /**
     * Starts and ends a group expression with 'less than' condition.
     *
     * @param $column Column.
     * @param $value  Value.
     */
    protected function _addSqlWhereLessThanCondition($column, $value)
    {
        $value = str_replace(['<', '>'], '', $value);
        
        if ($column === 'specials.specials_new_products_price' || $column === 'products.products_price') {
            $this->db->where($column . ' < ROUND(' . $value . $this->brutto . ', 4)');
            
            return;
        }
        
        $this->db->where($column . ' < ', $value);
    }
    
    
    /**
     * Starts and ends a group expression with 'more than' condition.
     *
     * @param $column Column.
     * @param $value  Value.
     */
    protected function _addSqlWhereMoreThanCondition($column, $value)
    {
        $value = str_replace(['<', '>'], '', $value);
        
        if ($column === 'specials.specials_new_products_price' || $column === 'products.products_price') {
            $this->db->where($column . ' > ROUND(' . $value . $this->brutto . ', 4)');
            
            return;
        }
        
        $this->db->where($column . ' > ', $value);
    }
    
    
    /**
     * Provides required columns.
     *
     * @return array Returns an array of the required columns.
     */
    protected function _columns()
    {
        $specialPriceOverviewColumns = [];
        
        foreach ($this->quickEditSpecialPricesOverviewColumns->serializeColumns() as $columns) {
            $specialPriceOverviewColumns[] = $columns['field'];
        }
        
        return array_merge($specialPriceOverviewColumns, $this->_addAdditionalColumns());
    }
    
    
    /**
     * Specifies the database relationships.
     */
    protected function _join()
    {
        $this->db->join('specials', 'specials.products_id = products.products_id', 'left');
        $this->db->join('products_description', 'products_description.products_id = products.products_id');
        $this->db->join('languages', 'languages.languages_id = ' . $_SESSION['languages_id']);
        $this->db->join('zones_to_geo_zones', 'zones_to_geo_zones.zone_country_id = ' . (int)STORE_COUNTRY);
        $this->db->join('tax_rates',
                        'tax_rates.tax_class_id = products.products_tax_class_id AND tax_rates.tax_zone_id = zones_to_geo_zones.geo_zone_id',
                        'left');
        
        $this->db->where('products_description.language_id = languages.languages_id');
    }
    
    
    /**
     * Returns the required columns for the overview of the properties.
     *
     * @return array Returns an array of the required columns for the overview of the properties.
     */
    protected function _delegateFilterToColumn()
    {
        $specialPriceOverviewColumns = [];
        
        foreach ($this->quickEditSpecialPricesOverviewColumns->serializeColumns() as $columns) {
            $specialPriceOverviewColumns[$columns['name']] = $columns['field'];
        }
        
        return $specialPriceOverviewColumns;
    }
    
    
    /**
     * Provides additionally required columns.
     *
     * @return array Returns an array of the additional columns.
     */
    protected function _addAdditionalColumns()
    {
        return [
            'products.products_id',
            'tax_rates.tax_rate',
        ];
    }
}