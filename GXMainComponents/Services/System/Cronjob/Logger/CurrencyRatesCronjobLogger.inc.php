<?php
/* --------------------------------------------------------------
   CurrencyRatesCronjobLogger.inc.php 2022-10-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class CurrencyRatesCronjobLogger extends AbstractCronjobLogger
{
    
    /**
     * @param array $context
     */
    public function log(array $context = [])
    {
        $this->logger->info('Currency Rates Update', $context);
    }
    
    
    /**
     * @param array $context
     */
    public function logError(array $context = [])
    {
        $this->logger->error('Currency Rates Update Error', $context);
    }
}
