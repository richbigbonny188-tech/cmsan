<?php
/* --------------------------------------------------------------
   CronjobMetaInterface.inc.php 2018-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CronjobMetaInterface
 */
interface CronjobMetaInterface
{
    /**
     * Returns true if the last cronjobs execution is less then one minute.
     *
     * @return bool True if cronjob execution works properly.
     */
    public function isActive();
    
    
    /**
     * Returns the last execution date of the cronjobs.
     *
     * @return \DateTime Last execution date of cronjobs.
     */
    public function executedAt();
}
