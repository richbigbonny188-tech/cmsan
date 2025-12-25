<?php
/* --------------------------------------------------------------
   DeleteLogsCronjobLogger.inc.php 2018-08-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DeleteLogsCronjobLogger
 */
class DeleteLogsCronjobLogger extends AbstractCronjobLogger
{
    /**
     * Adds a new log record.
     *
     * @param array $context (Optional) Additional information.
     *
     * @return void
     */
    public function log(array $context = [])
    {
        $this->logger->info('Delete Logfiles', $context);
    }
    
    
    /**
     * Adds a new error log record.
     *
     * @param array $context (Optional) Additional information.
     *
     * @return void
     */
    public function logError(array $context = [])
    {
        $this->logger->error('Failed to delete logfiles', $context);
    }
}