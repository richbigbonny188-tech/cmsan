<?php

/* --------------------------------------------------------------
   DirectHelpConfigurationStorage.inc.php 2018-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing the direct help configuration storage
 */
class DirectHelpConfigurationStorage extends ConfigurationStorage
{
    /**
     * Configuration namespace
     */
    const CONFIGURATION_NAMESPACE = 'modules/GambioDirectHelp';
    
    
    /**
     * Create instance
     */
    public function __construct()
    {
        parent::__construct(self::CONFIGURATION_NAMESPACE);
    }
    
    
    /**
     * Return whether the module is active
     *
     * @return bool
     */
    public function isModuleActive()
    {
        return $this->get('active') === '1';
    }
}