<?php
/* --------------------------------------------------------------
   SearchConditionStrategyTrait.inc.php 2018-07-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Trait SearchConditionStrategyTrait
 */
trait SearchConditionStrategyTrait
{
    /**
     * Determines the sql attribute and returns it.
     *
     * @param array $params
     * @param array $allowedColumns
     * @param array $allowedTables
     *
     * @return string
     *
     * @throws \InvalidSearchConditionException If given column or table is not allowed.
     */
    protected function getAttribute(array $params, array $allowedColumns, array $allowedTables)
    {
        $attribute = array_keys($params)[0];
        if (strpos($attribute, '.') !== false) {
            $attributeParts = explode('.', $attribute);
            $attributeParts = array_map(function ($element) {
                return addslashes($element);
            },
                $attributeParts);
            
            if (count($allowedColumns) > 0 && !in_array($attributeParts[1], $allowedColumns)) {
                throw new InvalidSearchConditionException("Column '$attributeParts[1]' is not allowed.");
            }
            if (count($allowedTables) > 0 && !in_array($attributeParts[0], $allowedTables)) {
                throw new InvalidSearchConditionException("Table '$attributeParts[0]' is not allowed.");
            }
            
            $attribute = implode('`.`', $attributeParts);
        } elseif (count($allowedColumns) > 0 && !in_array($attribute, $allowedColumns)) {
            throw new InvalidSearchConditionException("Column '$attribute' is not allowed.");
        } else {
            $attribute = addslashes($attribute);
        }
        
        return '`' . $attribute . '`';
    }
    
    
    /**
     * Determines the sql value and returns it.
     *
     * Notice: RangeStrategy and InStrategy have their own implementation.
     *
     * @param array $params
     *
     * @return string
     */
    protected function getValue(array $params)
    {
        $key = array_keys($params)[0];
        
        return '"' . addslashes($params[$key]) . '"';
    }
}