<?php
/*--------------------------------------------------------------------------------------------------
    ThemeBasicFileRepository.php 2019-10-11
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme\Repositories;

use FileNotFoundException;
use Gambio\StyleEdit\Core\Components\Theme\Json\ThemeInheritanceHandler;
use Gambio\StyleEdit\Core\Repositories\BasicFileRepository;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\TranslatedException;
use stdClass;

/**
 * Class ThemeBasicFileRepository
 * @package Gambio\StyleEdit\Core\Components\Theme\Repositories
 */
class ThemeBasicFileRepository extends BasicFileRepository
{
    /**
     * @param $filePath
     *
     * @return array|mixed
     * @throws FileNotFoundException
     */
    protected function loadConfigFromDisk($filePath): stdClass
    {
        $themeJsonPath = $filePath . DIRECTORY_SEPARATOR . 'theme.json';
        /**
         * @var ThemeInheritanceHandler $jsonInheritanceHandler
         */
        $jsonInheritanceHandler = SingletonPrototype::instance()->get(ThemeInheritanceHandler::class);
        $jsonInheritanceHandler->setFilename($themeJsonPath);
        $themeConfig = $jsonInheritanceHandler->execute();
        
        return $themeConfig;
    }
    
    
    /**
     * @param $themeId
     *
     * @return array|mixed
     * @throws TranslatedException
     */
    protected function loadOriginalConfigFromDisk($themeId): stdClass
    {
        $themeJsonPath = $this->configuration()->themesFolderPath() . $themeId . DIRECTORY_SEPARATOR . 'theme.json';
        
        if (!$this->fileIO()->exists($themeJsonPath)) {
            
            throw new TranslatedException('THEME_WITHOUT_CONFIGURATION', [$themeId, json_last_error_msg()]);
        }

        $themeConfig = $this->fileIO()->read($themeJsonPath);
        return $themeConfig;
    }
    
    
    /**
     * @param $config
     * @param $themePath
     *
     * @throws \Exception
     */
    protected function saveConfigToDisk($config, $themePath): void
    {
        $filePath = $themePath . DIRECTORY_SEPARATOR . 'theme.json';
        $this->fileIO()->write($config, $filePath);
    }
    
}