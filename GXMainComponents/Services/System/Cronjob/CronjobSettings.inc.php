<?php
/* --------------------------------------------------------------
   CronjobSettings.inc.php 2018-08-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CronjobSettings
 */
class CronjobSettings
{
    /**
     * @var string
     */
    protected $root;
    
    
    /**
     * CronjobSettings constructor.
     *
     * @param \StringType $root
     */
    public function __construct(ExistingDirectory $root)
    {
        $this->root = $root->getAbsolutePath();
    }
    
    
    public function getRoot()
    {
        return $this->root;
    }
    
    
    public function configurationDirectory()
    {
        return $this->root . '/GXMainComponents/Services/System/Cronjob/CronjobConfiguration/';
    }
    
    
    public function lastRunFlag()
    {
        return $this->root . '/cache/cronjobs/last_run';
    }
    
    
    public function lastCronjobSuccessFlag()
    {
        return $this->root . '/cache/cronjobs/last_success-';
    }
    
    
    public function lastCronjobRunFlag()
    {
        return $this->root . '/cache/cronjobs/last_run-';
    }
}