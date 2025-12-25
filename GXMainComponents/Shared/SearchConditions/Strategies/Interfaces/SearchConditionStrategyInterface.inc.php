<?php

/**
 * Interface SearchConditionStrategyInterface
 */
interface SearchConditionStrategyInterface
{
    /**
     * @return self
     */
    public static function create();
    
    
    /**
     * Parses the given params and returns the respective sql statement.
     *
     * @param \SearchConditionStrategyControl $strategyControl
     * @param array                           $params
     *
     * @return string
     */
    public function proceed(SearchConditionStrategyControl $strategyControl, array $params);
}