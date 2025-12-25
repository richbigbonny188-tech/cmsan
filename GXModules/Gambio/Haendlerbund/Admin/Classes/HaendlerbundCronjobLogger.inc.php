<?php
/* --------------------------------------------------------------
   HaendlerbundCronjobLogger.inc.php 2021-09-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class HaendlerbundCronjobLogger extends AbstractCronjobLogger
{
    
    /**
     * @param array $context
     */
    public function log(array $context = [])
    {
        $this->logger->info('Haendlerbund legal texts', $context);
    }
    
    
    /**
     * @param array $context
     */
    public function logError(array $context = [])
    {
        $this->logger->error('Haendlerbund legal texts error', $context);
    }
}
