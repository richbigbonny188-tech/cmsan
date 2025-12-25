<?php
/* --------------------------------------------------------------
   CronjobInterface.inc.php 2018-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CronjobInterface
 */
interface CronjobInterface
{
    /**
     * Returns the cronjob title.
     *
     * @return mixed
     */
    public function getTitle();
    
    
    /**
     * Returns the cronjob name.
     *
     * @return mixed
     */
    public function getName();
    
    
    /**
     * Returns the cronjob interval.
     *
     * @return string
     */
    public function getInterval();
    
    
    /**
     * Returns the cronjob status.
     *
     * @param \ExistingDirectory $cache Absolute path to cache directory.
     *
     * @return string
     */
    public function getStatus(\ExistingDirectory $cache);
    
    
    /**
     * Returns the cronjob log.
     *
     * @param \ExistingDirectory $logDir Absolute path to log files directory,
     *
     * @return string
     */
    public function getLog(\ExistingDirectory $logDir);
    
    
    /**
     * Returns the cronjob configuration.
     *
     * @return \CronjobConfigurationCollection
     */
    public function getConfiguration();
    
    
    /**
     * Returns the cronjob data as array.
     *
     * @param \ExistingDirectory $cacheDir
     *
     * @return array
     */
    public function toArray(\ExistingDirectory $cacheDir);
}
