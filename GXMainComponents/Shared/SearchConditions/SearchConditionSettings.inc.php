<?php
/* --------------------------------------------------------------
   SearchConditionSettings.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SearchConditionSettings
 */
class SearchConditionSettings implements SearchConditionSettingsInterface
{
    use SearchConditionStrategyTrait;
    
    /**
     * @var array
     */
    protected $strategies;
    
    /**
     * @var array
     */
    protected $allowedColumns;
    
    /**
     * @var array
     */
    protected $allowedTables;
    
    
    /**
     * SearchConditionSettings constructor.
     *
     * @param array $strategies
     * @param array $allowedColumns
     * @param array $allowedTables
     */
    public function __construct(array $strategies, array $allowedColumns, array $allowedTables)
    {
        $this->strategies     = $strategies;
        $this->allowedColumns = $allowedColumns;
        $this->allowedTables  = $allowedTables;
    }
    
    
    /**
     * Return a new object of this class.
     *
     * @param array|null $strategies
     * @param array|null $allowedColumns
     * @param array|null $allowedTables
     *
     * @return self
     */
    public static function create(array $allowedColumns = null, array $allowedTables = null, array $strategies = null)
    {
        if ($strategies === null) {
            $strategies = [
                'greater' => SearchConditionGreaterStrategy::create(),
                'geq'     => SearchConditionGreaterStrategy::create(),
                'in'      => SearchConditionInStrategy::create(),
                'leq'     => SearchConditionLowerStrategy::create(),
                'like'    => SearchConditionLikeStrategy::create(),
                'lower'   => SearchConditionLowerStrategy::create(),
                'match'   => SearchConditionMatchStrategy::create(),
                'neq'     => SearchConditionNotStrategy::create(),
                'not'     => SearchConditionNotStrategy::create(),
                'must'    => SearchConditionMustStrategy::create(),
                'range'   => SearchConditionRangeStrategy::create(),
                'should'  => SearchConditionShouldStrategy::create(),
            ];
        }
        if ($allowedColumns === null) {
            $allowedColumns = [];
        }
        if ($allowedTables === null) {
            $allowedTables = [];
        }
        
        return MainFactory::create(static::class, $strategies, $allowedColumns, $allowedTables);
    }
    
    
    /**
     * Returns the strategies as an array.
     *
     * @return array
     */
    public function strategies()
    {
        return $this->strategies;
    }
    
    
    /**
     * Returns the allowed columns as an array.
     *
     * @return array
     */
    public function allowedColumns()
    {
        return $this->allowedColumns;
    }
    
    
    /**
     * Returns the tables as an array.
     *
     * @return array
     */
    public function allowedTables()
    {
        return $this->allowedTables;
    }
}