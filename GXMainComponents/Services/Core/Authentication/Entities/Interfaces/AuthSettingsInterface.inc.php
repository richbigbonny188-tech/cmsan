<?php
/* --------------------------------------------------------------
   AuthSettingsInterface.inc.php 2016-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AuthSettingsInterface
 *
 * @category   System
 * @package    Authentication
 * @subpackage Interfaces
 */
interface AuthSettingsInterface
{
    /**
     * Returns the used authentication strategy.
     *
     * @return AuthStrategyInterface
     */
    public function getAuthStrategy();
    
    
    /**
     * Returns additional authentication strategies.
     *
     * @return AuthStrategyCollection
     */
    public function getAdditionalStrategies();
}