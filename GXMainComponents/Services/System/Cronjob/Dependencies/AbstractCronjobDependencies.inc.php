<?php
/* --------------------------------------------------------------
   AbstractCronjobDependencies.inc.php 2018-08-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractCronjobDependencies
 */
abstract class AbstractCronjobDependencies
{
    /**
     * @var \CronjobConfigurationStorage
     */
    protected $storage;
    
    
    /**
     * AbstractCronjobDependencies constructor.
     *
     * @param \CronjobConfigurationStorage $storage
     */
    public function __construct(CronjobConfigurationStorage $storage)
    {
        $this->storage = $storage;
    }
    
    
    /**
     * Returns an array of instantiated dependencies for cronjob services.
     *
     * @return array
     */
    abstract public function getDependencies();
}