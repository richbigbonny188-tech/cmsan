<?php
/*------------------------------------------------------------------------------
 RoutineLockerInterface.php 2021-10-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

interface RoutineLockerInterface
{
    /**
     * @return mixed
     * @throws RoutineLockedByAnotherInstanceException
     * @throws RoutineLockerException
     */
    public function acquireLock(): void;
    
    
    /**
     * @return void
     */
    public function releaseLock(): void;
    
    
    /**
     * @param $timeout
     */
    public function waitUntilLockIsReleasedOrTimeout($timeout): void;
    
    
    /**
     * @return bool
     */
    public function isLocked(): bool;
}
