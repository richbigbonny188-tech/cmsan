<?php
/* --------------------------------------------------------------
   LogFilesServiceInterface.inc.php 2018-08-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface LogFilesServiceInterface
 *
 * @category   System
 * @package    LogFiles
 * @subpackage Services
 */
interface LogFilesServiceInterface
{
    /**
     * Returns the deleted log files.
     *
     * @return $this|\LogFilesServiceInterface Same instance for chained method calls.
     */
    public function deleteOldLogFiles();
}