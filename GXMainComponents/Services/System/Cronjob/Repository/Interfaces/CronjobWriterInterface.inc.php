<?php
/* --------------------------------------------------------------
   CronjobWriterInterface.inc.php 2018-10-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CronjobWriterInterface
 */
interface CronjobWriterInterface
{
    /**
     * Saves cronjob configuration into the storage.
     *
     * @param \StringType         $cronjob
     * @param \KeyValueCollection $data
     *
     * @return $this|\CronjobWriterInterface Same instance for chained method calls.
     */
    public function save(StringType $cronjob, KeyValueCollection $data);
    
}