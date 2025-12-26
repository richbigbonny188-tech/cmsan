<?php
/* --------------------------------------------------------------
   CronjobServiceInterface.inc.php 2018-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CronjobServiceInterface
 */
interface CronjobServiceInterface
{
    /**
     * Returns all cronjobs.
     *
     * @return \CronjobCollection Collected cronjobs with meta data about execution.
     */
    public function getAll();
    
    
    /**
     * Returns a cronjob by the given identifier.
     *
     * @param \StringType $name Cronjob identifier.
     *
     * @return \Cronjob Cronjob of given identifier.
     */
    public function getByName(StringType $name);
    
    
    /**
     * Saves cronjob configuration into the storage.
     *
     * @param \StringType         $cronjob Name of configured cronjob.
     * @param \KeyValueCollection $data    Configuration data.
     *
     * @return void
     */
    public function save(StringType $cronjob, KeyValueCollection $data);
}