<?php
/* --------------------------------------------------------------
   CronjobReaderInterface.inc.php 2018-09-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CronjobReaderInterface
 */
interface CronjobReaderInterface
{
    /**
     * Returns all cronjobs.
     *
     * @return array Collected cronjobs with meta data about execution.
     */
    public function getAll();
    
    
    /**
     * Returns a cronjob by the given identifier.
     *
     * @param \StringType $name Cronjob identifier.
     *
     * @return array Cronjob of given identifier.
     */
    public function getByName(StringType $name);
}