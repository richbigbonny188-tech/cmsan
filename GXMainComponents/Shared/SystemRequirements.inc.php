<?php
/* --------------------------------------------------------------
   SystemRequirements.inc.php 2022-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

/**
 * Class SystemRequirements
 *
 * @category System
 * @package  Shared
 */
class SystemRequirements
{
    public static function getMySQLMinVersion(): string
    {
        return '5.7';
    }
    
    
    public static function getMySQLRecommendedVersion(): string
    {
        return '8.0';
    }
    
    
    public static function getMariaDBMinVersion(): string
    {
        return '10.2';
    }
    
    
    public static function getMariaDBRecommendedVersion(): string
    {
        return '10.9';
    }
    
    
    public static function getPhpMinVersion(): string
    {
        return '7.4';
    }
}
