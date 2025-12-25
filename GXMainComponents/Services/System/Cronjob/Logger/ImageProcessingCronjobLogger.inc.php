<?php
/* --------------------------------------------------------------
   ImageProcessingCronjobLogger.inc.php 2018-08-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ImageProcessingCronjobLogger
 */
class ImageProcessingCronjobLogger extends AbstractCronjobLogger
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
        $this->logger->info('Image Processing', $context);
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
        $this->logger->error('Unprocessable images', $context);
    }
}