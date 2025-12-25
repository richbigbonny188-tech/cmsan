<?php

/**
 * Interface SearchConditionSettingsInterface
 */
interface SearchConditionSettingsInterface
{
    /**
     * Return a new object of this class.
     *
     * @param array|null $strategies
     * @param array|null $allowedColumns
     * @param array|null $allowedTables
     *
     * @return self
     */
    public static function create(array $strategies = null, array $allowedColumns = null, array $allowedTables = null);
    
    
    /**
     * Returns the strategies as an array.
     *
     * @return array
     */
    public function strategies();
    
    
    /**
     * Returns the columns as an array.
     *
     * @return array
     */
    public function allowedColumns();
    
    
    /**
     * Returns the tables as an array.
     *
     * @return array
     */
    public function allowedTables();
}