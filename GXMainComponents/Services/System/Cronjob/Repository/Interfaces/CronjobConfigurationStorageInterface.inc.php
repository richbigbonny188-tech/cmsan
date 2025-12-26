<?php
/* --------------------------------------------------------------
   CronjobConfigurationStorageInterface.inc.php 2018-08-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CronjobConfigurationStorageInterface
 */
interface CronjobConfigurationStorageInterface
{
    /**
     * Returns a single configuration value by its key
     * if stored as json decode automaticaly
     *
     * @param string $key a configuration key (relative to the namespace prefix)
     *
     * @return string|array configuration value
     */
    public function get($key);
}