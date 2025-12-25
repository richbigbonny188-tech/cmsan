<?php
/*--------------------------------------------------------------------------------------------------
    CssRoutineLocker.php 2021-10-13
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


class CssRoutineLocker extends RoutineLocker
{
    private const LOCK_NAME = 'theme_build';
    
    
    /**
     * @param string $lockerDir
     *
     * @return RoutineLockerInterface
     */
    public static function create(string $lockerDir): RoutineLockerInterface
    {
        $existingDirectory = new ExistingDirectory($lockerDir);
        
        return new parent($existingDirectory, self::LOCK_NAME);
    }
}
