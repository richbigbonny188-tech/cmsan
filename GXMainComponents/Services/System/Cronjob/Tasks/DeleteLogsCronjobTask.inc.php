<?php
/* --------------------------------------------------------------
   DeleteLogsCronjobTask.inc.php 2018-08-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class DeleteLogsCronjobTask extends AbstractCronjobTask
{
    /**
     * Returns the cronjob callback.
     *
     * @param float $cronjobStartAsMicrotime
     *
     * @return \Closure
     */
    public function getCallback($cronjobStartAsMicrotime)
    {
        return function () {
            $this->logger->lastRun();
            
            $service = MainFactory::create_object('LogFilesService', $this->dependencies->getDependencies());
            $files   = $service->deleteOldLogFiles();
            
            if (array_key_exists('failed', $files)) {
                // error occurred
                $this->logger->logError($files);
                
                return;
            }
            $this->logger->log($files);
            $this->logger->lastSuccess();
        };
    }
}