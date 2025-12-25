<?php
/* --------------------------------------------------------------
   ModuleCenterModuleInterface.inc.php 2017-09-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ModuleCenterModuleInterface
 *
 * @category   System
 * @package    Modules
 * @subpackage Interfaces
 */
interface ModuleCenterModuleInterface
{
    /**
     * Installs the module
     */
    public function install();
    
    
    /**
     * Uninstalls the module
     */
    public function uninstall();
    
    
    /**
     * Returns true, if the module is installed. Otherwise false is returned.
     *
     * @return bool
     */
    public function isInstalled();
    
    
    /**
     * Returns true, if the module should be displayed in module center.
     *
     * @return bool
     */
    public function isVisible();
    
    
    /**
     * Returns the name of the module
     *
     * @return string
     */
    public function getName();
    
    
    /**
     * Returns the title of the module
     *
     * @return string
     */
    public function getTitle();
    
    
    /**
     * Returns the description of the module
     *
     * @return string
     */
    public function getDescription();
    
    
    /**
     * Returns the sort order of the module
     *
     * @return int
     */
    public function getSortOrder();
}