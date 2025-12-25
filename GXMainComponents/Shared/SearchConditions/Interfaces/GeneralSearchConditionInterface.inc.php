<?php

/**
 * Interface GeneralSearchConditionInterface
 */
interface GeneralSearchConditionInterface
{
    /**
     * SearchConditionInterface constructor.
     *
     * @param array                                    $conditions
     * @param \SearchConditionStrategyControlInterface $searchConditionStrategyControl
     */
    public function __construct(
        array $conditions,
        SearchConditionStrategyControlInterface $searchConditionStrategyControl
    );
    
    
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
    );
    
    
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
    );
    
    
    /**
     * Returns the respective sql where statement (without an beginning "WHERE").
     *
     * @return string
     */
    public function buildSql();
}