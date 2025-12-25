<?php
/* --------------------------------------------------------------
   AfterbuyStockupdateCronjobLogger.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);


/**
 * Class AfterbuyStockupdateCronjobLogger
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes
 */
class AfterbuyStockupdateCronjobLogger extends AbstractCronjobLogger
{
    /**
     * @param array $context
     *
     * @return void
     */
    public function log(array $context = []): void
    {
        if (!empty($context['message']) && !empty($context['level'])) {
            $this->logger->log($context['level'], $context['message']);
        } else {
            $this->logger->info('Afterbuy Stock sync', $context);
        }
    }
    
    
    /**
     * @param array $context
     *
     * @return void
     */
    public function logError(array $context = []): void
    {
        if (!empty($context['message'])) {
            $this->logger->error($context['message']);
        } else {
            $this->logger->error('Afterbuy Stock sync error', $context);
        }
    }
}
