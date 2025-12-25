<?php
/* --------------------------------------------------------------
   HaendlerbundCronjobTask.inc.php 2022-03-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use GXModules\Gambio\Haendlerbund\Admin\Classes\BatixService;
use GXModules\Gambio\Haendlerbund\Admin\Classes\Exceptions\BatixServiceException;
use GXModules\Gambio\Haendlerbund\Admin\Classes\HaendlerbundConfigurationFinder;
use GXModules\Gambio\Haendlerbund\Admin\Classes\HaendlerbundUpdateService;

class HaendlerbundCronjobTask extends AbstractCronjobTask
{
    
    /**
     * @param float $cronjobStartAsMicrotime
     *
     * @return Closure
     */
    public function getCallback($cronjobStartAsMicrotime)
    {
        return function () {
            $this->logger->lastRun();
            
            /** @var HaendlerbundConfigurationFinder $configuration */
            $configuration = $this->dependencies->getDependencies()['HaendlerbundConfigurationFinder'];
            if (!($configuration->moduleIsInstalled() && $configuration->moduleIsActive())) {
                return true;
            }
            
            /** @var HaendlerbundUpdateService $haendlerbundUpdateService */
            $haendlerbundUpdateService = $this->dependencies->getDependencies()['HaendlerbundUpdateService'];
            
            try {
                $haendlerbundUpdateService->updateLegalTexts();
                $this->logger->log(['update' => 'successful']);
            } catch (BatixServiceException $batixServiceException) {
                $this->logger->log(['error' => $batixServiceException->getMessage()]);
            }
            
            $this->logger->log(['CronjobTask finished' => date('c')]);
            $this->logger->lastSuccess();
            
            return true;
        };
    }
}