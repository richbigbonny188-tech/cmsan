<?php
/* --------------------------------------------------------------
   CronjobRepositoryInterface.inc.php 2018-10-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CronjobRepositoryInterface
 */
interface CronjobRepositoryInterface
{
    /**
     * Saves cronjob configuration into the storage.
     *
     * @param \CronjobInterface|\StringType $cronjob
     * @param \KeyValueCollection           $data
     *
     * @return void
     */
    public function save(StringType $cronjob, KeyValueCollection $data);
    
    
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
}