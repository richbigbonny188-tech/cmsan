<?php

/* --------------------------------------------------------------
   ErrorReportingModuleCenterModule.inc.php 2018-01-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a two-factor-authorization module center module
 */
class ErrorReportingModuleCenterModule extends AbstractModuleCenterModule
{
    /**
     * Language phrase section
     */
    const LANGUAGE_PHRASE_SECTION = 'error_reporting';
    
    
    /**
     * Initialize the module
     */
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('title', self::LANGUAGE_PHRASE_SECTION);
        $this->description = $this->languageTextManager->get_text('description', self::LANGUAGE_PHRASE_SECTION);
        $this->sortOrder   = 999997;
    }
    
    
    /**
     * Rewrite configuration file
     */
    private function writeConfigFile($isInstalled)
    {
        $configPath       = DIR_FS_CATALOG . 'GXModules/Gambio/ErrorReporting/configuration.json';
        $config           = json_decode(file_get_contents($configPath), true);
        $config['active'] = $isInstalled;
        
        file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
    
    
    /**
     * Install module
     */
    public function install()
    {
        parent::install();
        $this->writeConfigFile(true);
    }
    
    
    /**
     * Uninstall module
     */
    public function uninstall()
    {
        parent::uninstall();
        $this->writeConfigFile(false);
    }
    
    
    /**
     * Return whether the module is editable
     */
    public function isEditable()
    {
        return false;
    }
}