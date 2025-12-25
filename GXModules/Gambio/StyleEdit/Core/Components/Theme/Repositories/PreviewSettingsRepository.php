<?php
/*--------------------------------------------------------------------------------------------------
    PreviewSettingsRepository.php 2019-8-21
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme\Repositories;

use Gambio\StyleEdit\Core\Components\Theme\Entities\PreviewThemeSettings;
use Gambio\StyleEdit\Core\Repositories\BasicFileRepository;
use ReflectionException;

/**
 * Class PreviewSettingsRepository
 * @package Gambio\StyleEdit\Core\Components\Theme\Repositories
 */
class PreviewSettingsRepository extends BasicFileRepository
{
    /**
     * @param $themeId
     *
     * @return string
     * @throws ReflectionException
     */
    protected function path($themeId): string
    {
        return $this->configuration()->themesFolderPath() . $themeId . DIRECTORY_SEPARATOR . 'preview.json';
    }
    
    
    /**
     * @param PreviewThemeSettings $previewSettings
     *
     * @return bool|int
     * @throws ReflectionException
     */
    public function save(PreviewThemeSettings $previewSettings)
    {
        
        return $this->saveJsonFilesToDisk($previewSettings, $this->path($previewSettings->id()));
    }
    
    
    /**
     * @param $themeId
     *
     * @return PreviewThemeSettings
     * @throws ReflectionException
     */
    public function has($themeId): bool {
        return $this->fileIO()->exists($this->path($themeId));
    }

    /**
     * @param $themeId
     * @return PreviewThemeSettings
     * @throws ReflectionException
     */
    public function get($themeId): PreviewThemeSettings
    {
        $jsonObject = $this->loadJsonFilesFromDisk($this->path($themeId));
        
        return new PreviewThemeSettings($jsonObject->id, $jsonObject->publishPath, $jsonObject->compilePath);
    }
    
}