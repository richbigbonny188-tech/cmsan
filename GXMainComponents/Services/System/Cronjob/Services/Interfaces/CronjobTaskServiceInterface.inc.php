<?php
/* --------------------------------------------------------------
   CronjobTaskServiceInterface.inc.php 2018-08-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CronjobTaskServiceInterface
 */
interface CronjobTaskServiceInterface
{
    /**
     * Executes all valid cronjobs.
     *
     * @return void
     */
    public function run();
    
    
    /**
     * Returns the cronjob url.
     *
     * @return string
     */
    public static function getCronjobUrl();
}