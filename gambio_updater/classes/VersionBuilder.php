<?php
/* --------------------------------------------------------------
   VersionBuilder.php 2022-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Updater;

require_once __DIR__ . '/Version.php';

/**
 * Class VersionBuilder
 * @package Gambio\Updater
 */
class VersionBuilder
{
    /**
     * @param int      $gx
     * @param int      $major
     * @param int      $minor
     * @param int      $patch
     * @param int|null $beta
     * @param int|null $rc
     *
     * @return Version
     */
    public static function build(
        int $gx,
        int $major,
        int $minor,
        int $patch,
        ?int $beta = null,
        ?int $rc = null
    ): Version {
        $versionPart = "{$gx}.{$major}.{$minor}.{$patch}";
        $betaPart    = $beta !== null ? "BETA{$beta}" : '';
        $rcPart      = $rc !== null ? "RC{$rc}" : '';

        $version = $versionPart;
        if ($beta !== null) {
            $version = strtolower("{$versionPart} {$betaPart}");
        } elseif ($rc !== null) {
            $version = strtolower("{$versionPart}{$rcPart}");
        }

        $forDisplay = "v{$versionPart}";
        if ($beta !== null) {
            $forDisplay = "v{$versionPart} ({$betaPart})";
        } elseif ($rc !== null) {
            $forDisplay = "v{$versionPart} ({$rcPart})";
        }

        $internalName = "v{$versionPart}";
        if ($beta !== null) {
            $internalName = strtolower("v{$versionPart} {$betaPart}");
        } elseif ($rc !== null) {
            $internalName = strtolower("v{$versionPart} {$rcPart}");
        }

        $internalKey = $versionPart;
        if ($beta !== null) {
            $internalKey = strtolower("{$versionPart}_{$betaPart}");
        } elseif ($rc !== null) {
            $internalKey = strtolower("{$versionPart}_{$rcPart}");
        }

        return new Version($version, $forDisplay, $internalName, $internalKey);
    }


    /**
     * Converts version string to Version object. Example strings:
     * 4.5.1.0
     * v4.5.1.0
     * v4.5.1.0 (RC1)
     * v4.5.1.0 (beta1)
     * v4.5.1.0 (BETA2)
     * 4.5.1.0rc1
     * 4.5.1.0 beta2
     * 4.5.1.0_beta1
     * 
     * @param string $version
     *
     * @return Version
     */
    public static function buildFromString(string $version): Version
    {
        preg_match('/(?<gx>[\d]+)\.(?<major>[\d]+)\.(?<minor>[\d]+)\.(?<patch>[\d]+)(?:(.*beta\s*(?<beta>[\d]+))?)(?:(.*rc\s*(?<rc>[\d]+))?)/i',
                   $version,
                   $matches);

        return self::build(isset($matches['gx']) ? (int)$matches['gx'] : 0,
                           isset($matches['major']) ? (int)$matches['major'] : 0,
                           isset($matches['minor']) ? (int)$matches['minor'] : 0,
                           isset($matches['patch']) ? (int)$matches['patch'] : 0,
                           !empty($matches['beta']) ? (int)$matches['beta'] : null,
                           !empty($matches['rc']) ? (int)$matches['rc'] : null);
    }
}