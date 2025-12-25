<?php

/* --------------------------------------------------------------
   QuickEditProductPropertiesReader.inc.php 2020-02-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductPropertiesReader
 *
 * @todo       Improve values_price filtering.
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Repositories
 */
class QuickEditProductPropertiesReader implements QuickEditProductPropertiesReaderInterface
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
     * @var QuickEditPropertiesOverviewColumns $quickEditPropertiesOverviewColumns
     */
    protected $quickEditPropertiesOverviewColumns;
    
    
    /**
     * QuickEditProductPropertiesReader constructor.
     *
     * @param CI_DB_query_builder                $db                                  Database query builder instance.
     * @param QuickEditPropertiesOverviewColumns $quickEditPropertiesOverviewColumns  QuickEdit properties overview
     *                                                                                columns
     */
    public function __construct(
        CI_DB_query_builder $db,
        QuickEditPropertiesOverviewColumns $quickEditPropertiesOverviewColumns
    ) {
        $this->db                                 = $db;
        $this->quickEditPropertiesOverviewColumns = $quickEditPropertiesOverviewColumns;
        
        if (PRICE_IS_BRUTTO === 'true') {
            $this->brutto = ' / (1 + (`tax_rates`.`tax_rate` / 100))';
        }
    }
    
    
    /**
     * Returns filtered product properties based on the provided filter criteria.
     *
     * @param array $productIds       Array containing the selected product IDs to be processed.
     * @param array $filterParameters Contains the filter parameters.
     *
     * @return array Returns the query result as a pure array, or an empty array when no result is produced.
     */
    public function getFilteredProductProperties(array $productIds, array $filterParameters)
    {
        $this->_join();
        $propertiesOverviewColumns = $this->_delegateFilterToColumn();
        
        foreach ($filterParameters as $column => $value) {
            $value = preg_replace('/[^\w\s+<>*.,"\'-]/', '', $value);
            
            $valueWithoutLetters = preg_replace('/\D/', '', $value);
            
            if ($column === 'combiPrice' && empty($valueWithoutLetters)) {
                return [];
            }
            
            if (is_array($value)) {
                if ($column === 'combiShippingStatusName' || $column === 'combiPriceType') {
                    $this->_addSqlWhereGroupCondition($propertiesOverviewColumns[$column], $value);
                } else {
                    $this->_addSqlWhereBetweenCondition($propertiesOverviewColumns[$column], $value);
                }
                
                continue;
            }
            
            if (strpos($value, '<') === 0 || strpos($value, '>') >= 1) {
                $this->_addSqlWhereLessThanCondition($propertiesOverviewColumns[$column], $value);
                
                continue;
            }
            
            if (strpos($value, '>') === 0 || strpos($value, '<') >= 1) {
                $this->_addSqlWhereMoreThanCondition($propertiesOverviewColumns[$column], $value);
                
                continue;
            }
            
            if (strpos($value, '*') !== false) {
                $this->_addSqlWhereLikeCondition($propertiesOverviewColumns[$column], $value);
                
                continue;
            }
            
            $this->_addSqlWhereCondition($propertiesOverviewColumns[$column], $value);
        }
        
        return $this->db->select($this->_columns())
            ->where_in('products_properties_index.products_id', $productIds)
            ->group_by('products_properties_index.products_properties_combis_id, products.products_id')
            ->get('products_properties_index')
            ->result_array();
    }
    
    
    /**
     * Returns products that are subject to the specified filter criteria.
     *
     * @param array $productIds       Array containing the selected product IDs to be processed.
     * @param array $filterParameters Contains the filter parameters.
     *
     * @return int Returns the number of product properties found.
     */
    public function getFilteredProductPropertiesCount(array $productIds, array $filterParameters)
    {
        return count($this->getFilteredProductProperties($productIds, $filterParameters));
    }
    
    
    /**
     * Sets the starting point of the pagination and the number of products.
     *
     * @param IntType|null $start  Starting point.
     * @param IntType|null $length Number of products.
     *
     * @return QuickEditProductPropertiesReaderInterface Returns same instance for chained method calls.
     */
    public function paginateProperties(IntType $start = null, IntType $length = null)
    {
        $this->db->limit($length->asInt(), $start->asInt());
        
        return $this;
    }
    
    
    /**
     * Sets the sorting order of the products
     *
     * @param StringType|null $orderBy Sorting order (ASC or DESC)
     *
     * @return QuickEditProductPropertiesReaderInterface Returns same instance for chained method calls.
     */
    public function sortProperties(StringType $orderBy = null)
    {
        $this->db->order_by($orderBy->asString());
        
        return $this;
    }
    
    
    /**
     * Returns the number of all product properties found.
     *
     * @return int Returns the record number.
     */
    public function getProductPropertiesCount()
    {
        return (int)$this->db->count_all('products_properties_combis');
    }
    
    
    /**
     * Specifies the database relationships.
     */
    protected function _join()
    {
        $this->db->join('languages', 'languages.languages_id = ' . $_SESSION['languages_id']);
        $this->db->join('products_properties_combis',
                        'products_properties_combis.products_properties_combis_id = products_properties_index.products_properties_combis_id');
        $this->db->join('products', 'products.products_id = products_properties_index.products_id');
        $this->db->join('products_description', 'products_description.products_id = products.products_id');
        $this->db->join('tax_rates', 'tax_rates.tax_class_id = products.products_tax_class_id');
        $this->db->join('zones_to_geo_zones',
                        'zones_to_geo_zones.zone_country_id = ' . (int)STORE_COUNTRY
                        . ' AND tax_rates.tax_zone_id = zones_to_geo_zones.geo_zone_id');
        
        $this->db->where('products_description.language_id = languages.languages_id');
        $this->db->where('products_properties_index.language_id = languages.languages_id');
    }
    
    
    /**
     * Specifies the where conditions for the database query
     *
     * @param string $column Column name.
     * @param string $value  Condition value.
     */
    protected function _addSqlWhereCondition($column, $value)
    {
        if ($column === 'products_properties_combis.combi_price') {
            $this->_addSqlWhereRoundCondition($column, $value);
            
            return;
        }
        
        $this->db->where($column, $value);
    }
    
    
    /**
     * Starts and ends a group expression with 'where' condition in conjunction with a round function.
     *
     * @param string $column Column name.
     * @param string $value  Condition value.
     */
    protected function _addSqlWhereRoundCondition($column, $value)
    {
        $this->db->group_start()->where($column . ' = ROUND(' . $value . $this->brutto . ', 4)');
        $this->db->or_where('values_price = ROUND(' . $value . ', 4)');
        $this->db->group_end();
    }
    
    
    /**
     * Starts and ends a group expression with 'where' condition.
     *
     * @param string $column Column name.
     * @param array  $value  Condition value.
     */
    protected function _addSqlWhereGroupCondition($column, array $value)
    {
        $this->db->group_start()->where($column, array_shift($value));
        
        foreach ($value as $item) {
            $this->db->or_where($column, $item);
        }
        
        $this->db->group_end();
    }
    
    
    /**
     * Starts and ends a group expression with 'like' condition.
     *
     * @param string $column Column name.
     * @param string $value  Condition value.
     */
    protected function _addSqlWhereLikeCondition($column, $value)
    {
        $this->db->where($column . ' LIKE ', str_replace('*', '%', $value));
    }
    
    
    /**
     * Starts and ends a group expression with 'where between' condition.
     *
     * @param string $column Column name.
     * @param array  $value  Condition values.
     */
    protected function _addSqlWhereBetweenCondition($column, array $value)
    {
        $value = str_replace(['<', '>'], '', $value);
        
        if ($column === 'products_properties_combis.combi_price') {
            $this->db->group_start()->where($column . ' >= ROUND(' . reset($value) . $this->brutto . ', 4)');
            $this->db->where($column . ' <= ROUND(' . end($value) . $this->brutto . ', 4)');
            $this->db->where('values_price >= ROUND(' . reset($value) . ', 4)');
            $this->db->where('values_price <= ROUND(' . end($value) . ', 4)');
            $this->db->group_end();
            
            return;
        }
        
        $this->db->where($column . ' >= ' . reset($value));
        $this->db->where($column . ' <= ' . end($value));
    }
    
    
    /**
     * Starts and ends a group expression with 'less than' condition.
     *
     * @param string $column Column name.
     * @param string $value  Condition value.
     */
    protected function _addSqlWhereLessThanCondition($column, $value)
    {
        $value = str_replace(['<', '>'], '', $value);
        
        if ($column === 'products_properties_combis.combi_price') {
            $this->db->group_start()->where($column . ' < ROUND(' . $value . $this->brutto . ', 4)');
            $this->db->where('values_price < ROUND(' . $value . ', 4)');
            $this->db->group_end();
            
            return;
        }
        
        $this->db->where($column . ' < ', $value);
    }
    
    
    /**
     * Starts and ends a group expression with 'more than' condition.
     *
     * @param string $column Column name.
     * @param string $value  Condition value.
     */
    protected function _addSqlWhereMoreThanCondition($column, $value)
    {
        $value = str_replace(['<', '>'], '', $value);
        
        if ($column === 'products_properties_combis.combi_price') {
            $this->db->group_start()->where($column . ' > ROUND(' . $value . $this->brutto . ', 4)');
            $this->db->where('values_price > ROUND(' . $value . ', 4)');
            $this->db->group_end();
            
            return;
        }
        
        $this->db->where($column . ' > ', $value);
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
    
    
    /**
     * Provides required columns.
     *
     * @return array Returns an array of the required columns.
     */
    protected function _columns()
    {
        $propertiesOverviewColumns = [];
        
        foreach ($this->quickEditPropertiesOverviewColumns->serializeColumns() as $columns) {
            $propertiesOverviewColumns[] = $columns['field'];
        }
        
        return array_merge($propertiesOverviewColumns, $this->_addAdditionalColumns());
    }
    
    
    /**
     * Provides additionally required columns.
     *
     * @return array Returns an array of the additional columns.
     */
    protected function _addAdditionalColumns()
    {
        return [
            'products_description.products_name',
            'products_properties_combis.products_properties_combis_id as combi_id',
            'GROUP_CONCAT(DISTINCT products_properties_index.values_name SEPARATOR " / ") as combi_name',
            'SUM(products_properties_index.values_price) as values_price',
            'tax_rates.tax_rate',
        ];
    }
}
