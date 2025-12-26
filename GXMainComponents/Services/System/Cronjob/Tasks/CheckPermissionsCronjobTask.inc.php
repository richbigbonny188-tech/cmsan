<?php
/* --------------------------------------------------------------
   CheckPermissionsCronjobTask.inc.php 2018-09-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CheckPermissionsCronjobTask
 */
class CheckPermissionsCronjobTask extends AbstractCronjobTask
{
    /**
     * Returns a closure for execution in Jobby. If contains the logic for checking invalid permissions.
     *
     * @param float $cronjobStartAsMicrotime
     *
     * @return \Closure
     */
    public function getCallback($cronjobStartAsMicrotime)
    {
        return function () {
            $this->logger->lastRun();
            
            /** @var \DataCache $dataCache */
            $dataCache = $this->dependencies->getDependencies()['DataCache'];
            
            $shouldReadonlyList = SecurityCheck::getInvalidPermissionsNonWritableList();
            $notWritableList    = SecurityCheck::getInvalidPermissionsWritableList();
            
            $dataCache->write_persistent_data('should-readonly-list', $shouldReadonlyList);
            $dataCache->write_persistent_data('not-writable-list', $notWritableList);
            
            if (count($shouldReadonlyList)) {
                $this->logger->logError(['should-readonly' => $shouldReadonlyList]);
            }
            
            if (count($notWritableList)) {
                $this->logger->logError(['not-writable' => $notWritableList]);
            }
            
            $this->logger->log();
            
            $this->logger->lastSuccess();
            
            return true;
        };
    }
}
