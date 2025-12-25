<?php

/**
 * Interface SearchConditionStrategyControlInterface
 */
interface SearchConditionStrategyControlInterface
{
    /**
     * Creates a new SearchConditionStrategyControl object.
     *
     * @param \SearchConditionSettingsInterface $settings
     *
     * @return self
     */
    public static function create(SearchConditionSettingsInterface $settings = null);
    
    
    /**
     * Parses the given params and delegates to several strategies to build a sql statement, which will be returned.
     *
     * @param array $params
     *
     * @return string
     */
    public function buildSql(array $params);
    
    
    /**
     * Returns the internal settings object.
     *
     * @return \SearchConditionSettingsInterface
     */
    public function settings();
}