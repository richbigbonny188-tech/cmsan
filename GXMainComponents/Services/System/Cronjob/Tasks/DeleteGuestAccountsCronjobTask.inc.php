<?php
/* --------------------------------------------------------------
   DeleteGuestAccountsCronjobTask.inc.php 2018-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DeleteGuestAccountsCronjobTask
 */
class DeleteGuestAccountsCronjobTask extends AbstractCronjobTask
{
    /**
     * Returns a closure for execution in Jobby. It contains the logic for deleting guest accounts.
     *
     * @param float $cronjobStartAsMicrotime
     *
     * @return \Closure
     */
    public function getCallback($cronjobStartAsMicrotime)
    {
        return function () {
            $this->logger->lastRun();
            
            $logoffContentControl = $this->dependencies->getDependencies()['LogoffContentControl'];
            $deletedCustomerIds   = $logoffContentControl->delete_unused_guest_accounts();
            $this->logger->log($deletedCustomerIds);
            
            $this->logger->lastSuccess();
            
            return true;
        };
    }
}
