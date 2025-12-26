<?php
/* --------------------------------------------------------------
   AbstractDataPaginator.inc.php 2019-02-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

abstract class AbstractDataPaginator
{
    /**
     * @var \\CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * Applies the class default sorting
     */
    abstract protected function _applyDefaultSorting();
    
    
    /**
     * return the child class Field Map array.
     *
     * @return array.
     */
    
    abstract protected function _getFieldMap();
    
    
    /**
     * ManufacturerReader constructor.
     *
     * @param \CI_DB_query_builder $query_builder
     */
    public function __construct(CI_DB_query_builder $query_builder)
    {
        $this->db = $query_builder;
    }
    
    
    /**
     * Applies a sorting based on sorter param (if sorting is supplied) or apply default sorting.
     *
     * @param array $sorters Array of Sorter objects.
     *
     * @return $this|ProductListProvider Same instance for chained method calls.
     * @throws InvalidArgumentException if some element of the $sorters array is not a instance of Sorter object
     *
     */
    protected function _applySorting(array $sorters = [])
    {
        if (count($sorters) > 0) {
            foreach ($sorters as $sorter) {
                if ($sorter instanceof Sorter) {
                    $fieldName = $this->_translateToDatabaseField($sorter->alias());
                    $this->db->order_by($fieldName, $sorter->direction() === SORT_DESC ? 'desc' : 'asc');
                } else {
                    throw new InvalidArgumentException('Invalid sort configuration object');
                }
            }
        } else {
            $this->_applyDefaultSorting();
        }
        
        return $this;
    }
    
    
    /**
     * Return the related database field of a given object(Entity) field.
     *
     * @param \string $fieldName Object Field name.
     *
     * @return string.
     * @throws InvalidArgumentException if the provided field name is not a valid mapped field
     *
     */
    protected function _translateToDatabaseField($fieldName)
    {
        if (array_key_exists(strtolower($fieldName), $this->_getFieldMap())) {
            return $this->_getFieldMap()[strtolower($fieldName)];
        } else {
            throw new InvalidArgumentException("The field '{$fieldName}' is not a valid field name.");
        }
    }
    
    
    /**
     * Applies a pagination (Limit and Offset) clause to the currently building query.
     *
     * @param \Pager|null $pager (Optional) Pager object with pagination information
     *
     * @return $this|ProductListProvider Same instance for chained method calls.
     */
    protected function _applyPagination(Pager $pager = null)
    {
        if ($pager !== null) {
            $this->db->limit($pager->perPage(), $pager->offset());
        }
        
        return $this;
    }
    
    
    /**
     * Parse an order by sql query into a array with fieldname and direction.
     *
     * @param StringType|null $orderBy SQL order by String
     *
     * @return array with sort information structured as ['field'=>'', 'direction'=>''].
     */
    protected function _parseOrderByString(StringType $orderBy = null)
    {
        $qb_orderBy = [];
        foreach (explode(',', $orderBy->asString()) as $field) {
            $qb_orderBy[] = preg_match('/\s+(ASC|DESC)$/i', rtrim($field), $match, PREG_OFFSET_CAPTURE) ? [
                'field'     => ltrim(substr($field, 0, $match[0][1])),
                'direction' => strtolower($match[1][0])
            ] : ['field' => trim($field), 'direction' => 'asc'];
        }
        
        return $qb_orderBy;
    }
    
    
    /**
     * Parse an order by sql query into a array with fieldname and direction.
     *
     * @param StringType $databaseFieldName the database field name. It can be supplied as table.fieldName or fieldName.
     *
     * @return string|FALSE will return the field name or FALSE if the field doesn't exist.
     */
    protected function _translateToJsonFieldName(StringType $databaseFieldName)
    {
        $jsonFieldName = array_search(strtolower($databaseFieldName->asString()),
                                      array_map('strtolower', $this->_getFieldMap()),
                                      true);
        if (!$jsonFieldName) {
            /*remove all the table name the values of the map*/
            $databaseFieldNamesArray = array_map(function ($array_value) {
                $arr_value = explode('.', $array_value);
                
                return strtolower($arr_value[count($arr_value) - 1]);
            },
                $this->_getFieldMap());
            
            $jsonFieldName = array_search(strtolower($databaseFieldName->asString()), $databaseFieldNamesArray, true);
        }
        
        return $jsonFieldName;
    }
    
    
    /**
     * Translate an order by sql instruction into a array of Sorter
     *
     * @param StringType|null $orderBy SQL instruction with fields to sort.
     *
     * @return array of Sorter objects.
     */
    public function _translateOrderByStringIntoArrayOfSorter(StringType $orderBy = null)
    {
        $result = [];
        if ($orderBy && !empty($orderBy->asString())) {
            $qb_orderBy = $this->_parseOrderByString($orderBy);
            
            foreach ($qb_orderBy as $sortInfo) {
                $jsonFieldName = $this->_translateToJsonFieldName(new StringType($sortInfo['field']));
                
                if (!$jsonFieldName) {
                    throw  new InvalidArgumentException("Invalid order by field '{$sortInfo['field']}'");
                }
                
                $direction = (strtolower($sortInfo['direction']) === 'asc') ? '+' : '-';
                $result[]  = new Sorter(new StringType($direction . $jsonFieldName));
            }
        }
        
        return $result;
    }
}