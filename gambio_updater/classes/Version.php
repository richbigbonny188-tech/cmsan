<?php
/* --------------------------------------------------------------
   Version.php 2021-04-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Updater;

/**
 * Class Version
 * @package Gambio\Updater
 */
class Version
{
    /** @var string $version */
    private $version;

    /** @var string $forDisplay */
    private $forDisplay;

    /** @var string $internalName */
    private $internalName;

    /** @var string $internalKey */
    private $internalKey;


    /**
     * Version constructor.
     *
     * @param string $version
     * @param string $forDisplay
     * @param string $internalName
     * @param string $internalKey
     */
    public function __construct(string $version, string $forDisplay, string $internalName, string $internalKey)
    {
        $this->version      = $version;
        $this->forDisplay   = $forDisplay;
        $this->internalName = $internalName;
        $this->internalKey  = $internalKey;
    }


    /**
     * @return string
     */
    public function version(): string
    {
        return $this->version;
    }


    /**
     * @return string
     */
    public function forDisplay(): string
    {
        return $this->forDisplay;
    }


    /**
     * @return string
     */
    public function internalName(): string
    {
        return $this->internalName;
    }


    /**
     * @return string
     */
    public function internalKey(): string
    {
        return $this->internalKey;
    }


    /**
     * @return string
     */
    public function updateDirectoryName(): string
    {
        return 'v' . $this->versionWithoutPreFinalSuffix();
    }


    /**
     * @return int|null
     */
    public function betaVersion(): ?int
    {
        $version = explode('beta', $this->version);
        if (isset($version[1])) {
            return (int)$version[1];
        }

        return null;
    }


    /**
     * @return int|null
     */
    public function rcVersion(): ?int
    {
        $version = explode('rc', $this->version);
        if (isset($version[1])) {
            return (int)$version[1];
        }

        return null;
    }


    /**
     * @return string
     */
    public function previousMasterUpdate(): string
    {
        $version = explode('.', $this->versionWithoutPreFinalSuffix());

        if ((int)$version[1] % 2 === 0 && (int)$version[2] === 0) {
            return $version[0] . '.' . ($version[1] - 2) . '.0.0';
        }

        return $version[0] . '.' . ($version[1] % 2 === 0 ? $version[1] : $version[1] - 1) . '.0.0';
    }


    /**
     * @return string|null
     */
    public function previousServicePack(): ?string
    {
        $versions = $this->getUpdateVersions();

        $version = $this->versionWithoutPreFinalSuffix();
        $key     = array_search($version, $versions, true);

        if ($key !== false) {
            for ($i = $key - 1; $i >= 0; $i--) {
                if (version_compare($versions[$i], substr($version, 0, strrpos($version, '.')), '<')) {
                    return substr($versions[$i], 0, strrpos($versions[$i], '.')) . '.0';
                }
            }
        }

        return null;
    }


    /**
     * @return string|null
     */
    public function minimumUpdateVersion(): ?string
    {
        $version = $this->versionWithoutPreFinalSuffix();

        $versions = $this->getUpdateVersions();

        $key = array_search($version, $versions, true);

        return $versions[$key - 1] ?? null;
    }


    /**
     * @return string|null
     */
    public function maximumUpdateVersion(): ?string
    {
        $version = $this->versionWithoutPreFinalSuffix();

        if ($version !== $this->version && $this->betaVersion() !== 1) {
            if ($this->betaVersion() > 1) {
                return $version . ' beta' . ($this->betaVersion() - 1);
            }
            if ($this->rcVersion() > 1) {
                return $version . 'rc' . ($this->rcVersion() - 1);
            }
            if ($this->rcVersion() === 1) {
                return $version . ' beta5';
            }

            return $this->version;
        }

        if ((int)explode('.', $this->version)[3] === 0 && $this->betaVersion() !== 1) {
            return $version . 'rc5';
        }

        return $this->minimumUpdateVersion();
    }


    /**
     * @return bool
     */
    public function isMasterUpdate(): bool
    {
        $versionParts = explode('.', $this->version);

        return isset($versionParts[1], $versionParts[2]) && (int)$versionParts[1] % 2 === 0
               && (int)$versionParts[2] === 0;
    }


    /**
     * @param string $prefix
     *
     * @return string
     */
    public function versionInfoFilename(string $prefix = 'full'): string
    {
        if (!in_array($prefix, ['full', 'ku', 'mu', 'sp'])) {
            $prefix = 'full';
        }

        return $prefix . '_' . str_replace('_', '-', $this->internalKey);
    }


    /**
     * @return array|false[]|mixed|string[]
     */
    private function getUpdateVersions()
    {
        $versions = array_map(function ($update) {
            return substr(basename($update), 1);
        },
            glob(__DIR__ . '/../updates/v*', GLOB_ONLYDIR));
        usort($versions, 'version_compare');

        return $versions;
    }


    /**
     * @return mixed|string
     */
    private function versionWithoutPreFinalSuffix()
    {
        return trim(preg_split('/[br]/', $this->version)[0]);
    }
}
