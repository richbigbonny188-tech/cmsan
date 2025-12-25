<?php
/* --------------------------------------------------------------
   GXCoreLoaderInterface.inc.php 2019-09-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface GXCoreLoaderInterface
 *
 * @category   System
 * @package    Loaders
 * @subpackage Interfaces
 */
interface GXCoreLoaderInterface
{
    /**
     * Get Service Object
     *
     * @param string $serviceName
     *
     * @return AddressBookService|CountryService|CustomerService
     * @throws DomainException
     */
    public function getService($serviceName);
    
    
    /**
     * Get a CodeIgniter Query Builder Object
     *
     * @return CI_DB_query_builder
     */
    public function getDatabaseQueryBuilder();
    
    
    /**
     * Method depends on the PHP DebugBar library.
     *
     * @return DebugBar
     */
    public function getDebugBar();
    
    
    /**
     * Method depends on the PHP DebugBar library.
     *
     * @return array Returns array with the <head> and <body> HTML assets.
     */
    public function getDebugBarAssets();
    
    
    /**
     * Returns an instance of the ThemeControl.
     *
     * @return \ThemeControl
     */
    public function getThemeControl();
    
    
    /**
     * Database settings for theme/template active
     * @return ViewSettings
     */
    public function getViewSettings(): ViewSettings;
}
