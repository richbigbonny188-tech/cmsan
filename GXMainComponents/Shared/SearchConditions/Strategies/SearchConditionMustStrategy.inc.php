<?php
/* --------------------------------------------------------------
   SearchConditionMustStrategy.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SearchConditionMustStrategy
 */
class SearchConditionMustStrategy implements SearchConditionStrategyInterface
{
    use SearchConditionStrategyTrait;
    
    
    /**
     * Return a new object of this class.
     *
     * @return self
     */
    public static function create()
    {
        return MainFactory::create(static::class);
    }
    
    
    /**
     * Parses the given params and returns the respective sql statement.
     *
     * @param \SearchConditionStrategyControl $strategyControl
     * @param array                           $params
     *
     * @return string
     */
    public function proceed(SearchConditionStrategyControl $strategyControl, array $params)
    {
        $sqlParts = [];
        
        if ($this->isAssocArray($params['must'])) {
            $sqlParts[] = $strategyControl->buildSql($params['must']);
        } else {
            foreach ($params['must'] as $condition) {
                $sqlParts[] = $strategyControl->buildSql($condition);
            }
        }
        
        return '(' . implode(' AND ', $sqlParts) . ')';
    }
    
    
    /**
     * @param array $array
     *
     * @return bool
     */
    public function isAssocArray(array $array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}