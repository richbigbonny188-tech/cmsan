<?php
/* --------------------------------------------------------------
   SearchConditionShouldStrategy.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SearchConditionShouldStrategy
 */
class SearchConditionShouldStrategy implements SearchConditionStrategyInterface
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
        
        if ($this->isAssocArray($params['should'])) {
            $sqlParts[] = $strategyControl->buildSql($params['should']);
        } else {
            foreach ($params['should'] as $condition) {
                $sqlParts[] = $strategyControl->buildSql($condition);
            }
        }
        
        return '(' . implode(' OR ', $sqlParts) . ')';
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