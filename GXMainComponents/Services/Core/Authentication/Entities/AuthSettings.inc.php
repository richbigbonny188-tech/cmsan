<?php
/* --------------------------------------------------------------
   AuthSettings.inc.php 2016-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AuthSettings
 *
 * @category   System
 * @package    Authentication
 * @subpackage Entities
 */
class AuthSettings implements AuthSettingsInterface
{
    /**
     * @var AuthStrategyInterface $authStrategy
     */
    protected $authStrategy;
    
    /**
     * @var AuthStrategyCollection $additionalStrategies
     */
    protected $additionalStrategies;
    
    
    /**
     * AuthSettings constructor.
     *
     * @param AuthStrategyInterface  $authStrategy
     * @param AuthStrategyCollection $additionalStrategies
     */
    public function __construct(AuthStrategyInterface $authStrategy, AuthStrategyCollection $additionalStrategies)
    {
        $this->authStrategy         = $authStrategy;
        $this->additionalStrategies = $additionalStrategies;
    }
    
    
    /**
     * Returns the used authentication strategy.
     *
     * @return AuthStrategyInterface
     */
    public function getAuthStrategy()
    {
        return $this->authStrategy;
    }
    
    
    /**
     * Returns additional authentication strategies.
     *
     * @return AuthStrategyCollection
     */
    public function getAdditionalStrategies()
    {
        return $this->additionalStrategies;
    }
}