<?php
/*--------------------------------------------------------------------------------------------------
    InheritanceMapper.php 2022-06-21
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme\Entities;

use Exception;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\BasicThemeInterface;
use Gambio\StyleEdit\Core\Components\Theme\Exceptions\InvalidThemeJsonException;
use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\StyleEditConfiguration;
use Gambio\StyleEdit\Core\Components\Theme\Exceptions\ThemeIdNotSuppliedException;

/**
 * Class ThemeInheritanceMapper
 * @package Gambio\StyleEdit\Core\Components\Theme\Entities
 */
class ThemeInheritanceMapper
{
    
    /**
     * @var StyleEditConfiguration
     */
    private $config;
    /**
     * @var FileIO
     */
    private $fileIO;
    
    
    /**
     * ThemeInheritanceMapper constructor.
     *
     * @param FileIO                 $fileIO
     * @param StyleEditConfiguration $config
     */
    public function __construct(FileIO $fileIO, StyleEditConfiguration $config)
    {
        
        $this->fileIO = $fileIO;
        $this->config = $config;
    }
    
    
    /**
     * @param string $themeId
     *
     * @return BasicThemeInterface
     * @throws Exception
     */
    public function createBasicThemeFor(string $themeId): BasicThemeInterface
    {
        $theme  = $this->getThemeObject($themeId);
        $parent = empty($theme->extends) ? null : $this->createBasicThemeFor($theme->extends);
        
        return new BasicTheme($themeId, $parent);
    }
    
    
    /**
     * @param string $themeId
     *
     * @return array|mixed
     * @throws Exception
     */
    protected function getThemeObject(string $themeId)
    {
        $path = $this->config->themesFolderPath() . DIRECTORY_SEPARATOR . $themeId .DIRECTORY_SEPARATOR. 'theme.json';
        if ($this->fileIO->exists($path)) {
            try {
                return $this->fileIO->read($path);
            } catch (\Throwable $exception) {
                throw new InvalidThemeJsonException([$path]);
            }
        }

        throw new ThemeIdNotSuppliedException([$themeId]);
    }
}