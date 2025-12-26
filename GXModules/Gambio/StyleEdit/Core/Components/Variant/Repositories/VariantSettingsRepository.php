<?php
/*--------------------------------------------------------------------------------------------------
    VariantSettingsRepository.php 2020-02-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Variant\Repositories;

use Exception;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\AlwaysNewStrategyInterface;
use Gambio\StyleEdit\Core\Repositories\BasicFileRepository;
use Gambio\StyleEdit\Core\Repositories\SettingsRepository;
use Gambio\StyleEdit\Core\Repositories\Entities\ConfigurationCollection;

/**
 * Class VariantConfigurationRepository
 * @package Gambio\StyleEdit\Core\Components\Variant\Repositories
 */
class VariantSettingsRepository extends BasicFileRepository implements AlwaysNewStrategyInterface
{
    /**
     * @param string $themeId
     * @param string $variantDir
     *
     * @return array|mixed
     * @throws Exception
     */
    public function exists(string $themeId, string $variantDir): bool
    {
        return $this->fileIO()->exists($this->getSettingsFilename($themeId, $variantDir));
    }
    
    
    /**
     * @param string $themeId
     * @param string $directory
     *
     * @return string
     */
    protected function getSettingsFilename(string $themeId, string $directory): string
    {
        return implode(DIRECTORY_SEPARATOR,
                       [
                           $this->styleEditConfiguration->themesFolderPath(),
                           $themeId,
                           $directory,
                           'settings.json'
                       ]);
    }
    
    
    /**
     * @param string $themeId
     * @param string $variantDir
     *
     * @return ConfigurationCollection
     * @throws Exception
     */
    public function getAllFrom(string $themeId, string $variantDir): ConfigurationCollection
    {
        $filename   = $this->getSettingsFilename($themeId, $variantDir);
        $jsonObject = $this->loadJsonFilesFromDisk($filename);
        return ConfigurationCollection::createFromJsonList($jsonObject);
    }


    /**
     * @param string $themeId
     * @param string $variantDir
     * @param ConfigurationCollection $list
     *
     * @return void
     * @throws Exception
     */
    public function setAllFrom(string $themeId, string $variantDir, ConfigurationCollection $list): void
    {
        $filename   = $this->getSettingsFilename($themeId, $variantDir);

        if ($this->fileIO()->exists($filename)) {
            $json = $list->jsonSerialize();
            $this->saveJsonFilesToDisk($json, $filename);
        }
    }
}