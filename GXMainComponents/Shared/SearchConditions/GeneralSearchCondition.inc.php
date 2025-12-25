<?php
/* --------------------------------------------------------------
   GeneralSearchCondition.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GeneralSearchCondition
 */
class GeneralSearchCondition implements GeneralSearchConditionInterface
{
    /**
     * @var array
     */
    protected $conditions;
    
    /**
     * @var \SearchConditionStrategyControlInterface
     */
    protected $strategyControl;
    
    
    public function __construct(
        array $conditions,
        SearchConditionStrategyControlInterface $searchConditionStrategyControl
    ) {
        $this->conditions      = $conditions;
        $this->strategyControl = $searchConditionStrategyControl;
        
        if (isset($this->conditions['search'])) {
            $this->conditions = $this->conditions['search'];
        }
    }
    
    
    /**
     * Return a new object of this class by a given parameterized conditions array.
     *
     * @param array                                         $array
     * @param null|\SearchConditionStrategyControlInterface $searchConditionStrategyControl
     *
     * @return self
     */
    public static function createByArray(
        array $array,
        SearchConditionStrategyControlInterface $searchConditionStrategyControl = null
    ) {
        if ($searchConditionStrategyControl === null) {
            $settings = SearchConditionSettings::create(static::allowedColumns(), static::allowedTables());
            
            $searchConditionStrategyControl = SearchConditionStrategyControl::create($settings);
        }
        
        return MainFactory::create(static::class, $array, $searchConditionStrategyControl);
    }
    
    
    /**
     * Return a new object of this class by a given parameterized conditions json.
     *
     * @param NonEmptyStringType                            $json
     * @param null|\SearchConditionStrategyControlInterface $searchConditionStrategyControl
     *
     * @return self
     */
    public static function createByJson(
        NonEmptyStringType $json,
        SearchConditionStrategyControlInterface $searchConditionStrategyControl = null
    ) {
        if ($searchConditionStrategyControl === null) {
            $settings = SearchConditionSettings::create(static::allowedColumns(), static::allowedTables());
            
            $searchConditionStrategyControl = SearchConditionStrategyControl::create($settings);
        }
        
        return MainFactory::create(static::class,
                                   json_decode($json->asString(), true),
                                   $searchConditionStrategyControl);
    }
    
    
    /**
     * Returns the respective sql where statement (without an beginning "WHERE").
     *
     * @return string
     */
    public function buildSql()
    {
        $sqlParts = [];
        
        if ($this->isAssocArray($this->conditions)) {
            foreach ($this->conditions as $key => $condition) {
                $sqlParts[] = $this->strategyControl->buildSql([$key => $condition]);
            }
        } else {
            foreach ($this->conditions as $condition) {
                $sqlParts[] = $this->strategyControl->buildSql($condition);
            }
        }
        
        return implode(' AND ', $sqlParts);
    }
    
    
    /**
     * @return bool
     */
    public function isAssocArray(array $array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
    
    
    /**
     * Return the allowed columns as an array.
     *
     * @return array
     */
    protected static function allowedColumns()
    {
        return [];
    }
    
    
    /**
     * Return the allowed tables as an array.
     *
     * @return array
     */
    protected static function allowedTables()
    {
        return [];
    }
}