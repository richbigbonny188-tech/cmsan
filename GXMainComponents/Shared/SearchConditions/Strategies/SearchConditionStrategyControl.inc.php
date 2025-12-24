<?php
/* --------------------------------------------------------------
   SearchConditionStrategyControl.inc.php 2018-07-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SearchConditionStrategyControl
 */
class SearchConditionStrategyControl implements SearchConditionStrategyControlInterface
{
    use SearchConditionStrategyTrait;
    
    /**
     * @var SearchConditionSettingsInterface
     */
    protected $settings;
    
    
    /**
     * SearchConditionStrategy constructor.
     *
     * @param array $strategies
     */
    public function __construct(SearchConditionSettingsInterface $settings)
    {
        $this->settings = $settings;
    }
    
    
    /**
     * Return a new object of this class.
     *
     * @param \SearchConditionSettingsInterface $settings
     *
     * @return self
     */
    public static function create(SearchConditionSettingsInterface $settings = null)
    {
        if ($settings === null) {
            $settings = SearchConditionSettings::create();
        }
        
        return MainFactory::create(static::class, $settings);
    }
    
    
    /**
     * Parses the given params and delegates to several strategies to build a sql statement, which will be returned.
     *
     * @param array $params
     *
     * @return string
     *
     * @throws \InvalidSearchConditionException If operation is unknown.
     */
    public function buildSql(array $params)
    {
        if (count($params) === 0) {
            return '';
        }
        
        $operation = array_keys($params)[0];
        
        if (isset($this->settings->strategies()[$operation])) {
            return $this->settings->strategies()[$operation]->proceed($this, $params);
        }
        
        throw new InvalidSearchConditionException('Search condition contains an unknown operation: ' . $operation);
    }
    
    
    /**
     * Returns the internal settings object.
     *
     * @return \SearchConditionSettingsInterface
     */
    public function settings()
    {
        return $this->settings;
    }
}