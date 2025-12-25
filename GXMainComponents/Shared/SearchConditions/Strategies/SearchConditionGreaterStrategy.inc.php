<?php
/* --------------------------------------------------------------
   SearchConditionGreaterStrategy.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SearchConditionGreaterStrategy
 */
class SearchConditionGreaterStrategy implements SearchConditionStrategyInterface
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
     *
     * @throws \Exception
     */
    public function proceed(SearchConditionStrategyControl $strategyControl, array $params)
    {
        if (isset($params['greater'])) {
            $params    = $params['greater'];
            $operation = '>';
        } else {
            $params    = $params['geq'];
            $operation = '>=';
        }
        
        $attribute = $this->getAttribute($params,
                                         $strategyControl->settings()->allowedColumns(),
                                         $strategyControl->settings()->allowedTables());
        $value     = $this->getValue($params);
        
        return $attribute . ' ' . $operation . ' ' . $value;
    }
}