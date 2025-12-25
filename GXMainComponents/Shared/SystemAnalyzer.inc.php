<?php
/* --------------------------------------------------------------
   SystemAnalyzer.inc.php 2023-04-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

/**
 * Class SystemAnalyzer
 *
 * @category System
 * @package  Shared
 */
class SystemAnalyzer
{
    /**
     * @param SystemRequirements  $requirements
     * @param LanguageTextManager $textManager
     *
     * @return string
     */
    public static function getDatabaseRequirementsMesssage(
        SystemRequirements  $requirements,
        LanguageTextManager $textManager
    ): string {
        if (self::areDatabaseRequirementsMet($requirements)
            || gm_get_conf('SUPPRESS_DB_REQUIREMENTS_WARNING') === '1') {
            return '';
        }
        
        $dbVersion = self::getMysqliServerVersion();
        
        if (self::isMariaDB()) {
            return sprintf($textManager->get_text('mariadb_requirements_warning', 'system'),
                           $dbVersion,
                           $requirements::getMariaDBMinVersion(),
                           $requirements::getMariaDBRecommendedVersion());
        }
        
        return sprintf($textManager->get_text('mysql_requirements_warning', 'system'),
                       $dbVersion,
                       $requirements::getMySQLMinVersion(),
                       $requirements::getMySQLRecommendedVersion());
    }
    
    
    /**
     * @return string
     */
    public static function getMysqliServerVersion(): string
    {
        $version = (string)mysqli_get_server_version(self::getSqlConnection());
        
        $versionParts = [
            (int)substr($version, 0, -4),
            (int)substr($version, -4, 2),
            (int)substr($version, -2),
        ];
        
        return implode('.', $versionParts);
    }
    
    
    /**
     * @return bool
     */
    public static function isMariaDB(): bool
    {
        return stripos(mysqli_get_server_info(self::getSqlConnection()), 'MariaDB') !== false;
    }
    
    
    /**
     * @param SystemRequirements $requirements
     *
     * @return bool
     */
    public static function areDatabaseRequirementsMet(SystemRequirements $requirements): bool
    {
        return version_compare(self::getMysqliServerVersion(),
                               self::isMariaDB() ? $requirements::getMariaDBMinVersion() : $requirements::getMySQLMinVersion(),
                               '>=');
    }
    
    
    /**
     * @param SystemRequirements $requirements
     *
     * @return bool
     */
    public static function arePhpRequirementsMet(SystemRequirements $requirements): bool
    {
        return version_compare(PHP_VERSION, $requirements::getPhpMinVersion(), '>=');
    }
    
    
    /**
     * @return string
     */
    public static function getPhpVersion(): string
    {
        return PHP_VERSION;
    }
    
    
    /**
     * @return mixed
     */
    private static function getSqlConnection()
    {
        if (!defined('DB_SERVER') && empty($GLOBALS["___mysqli_ston"])) {
            include_once __DIR__ . '/../../includes/configure.php';
        }
        
        include_once __DIR__ . '/../../inc/xtc_db_connect.inc.php';
        
        return $GLOBALS["___mysqli_ston"] ?? xtc_db_connect();
    }
}
