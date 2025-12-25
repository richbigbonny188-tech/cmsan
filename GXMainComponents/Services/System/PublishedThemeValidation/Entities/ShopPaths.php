<?php
/* --------------------------------------------------------------
  ShopPaths.php 2019-12-12
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class ShopPaths
 */
class ShopPaths implements ShopPathsInterface
{
    /**
     * @var string
     */
    protected $webPath;
    
    /**
     * @var string
     */
    protected const CACHE_FILE_PATH = 'cache/published_theme_path_cache.txt';
    
    protected const PUBLISHED_THEME_PATH = 'public/theme';
    
    /**
     * ShopBaseUrl constructor.
     *
     * @param string $webPath
     */
    public function __construct(string $webPath)
    {
        $this->webPath = $webPath;
    }
    
    
    /**
     * @inheritDoc
     */
    public function webPath(): string
    {
        return $this->webPath;
    }
    
    
    /**
     * @inheritDoc
     */
    public function cacheFilePath(): string
    {
        return self::CACHE_FILE_PATH;
    }
    
    
    /**
     * @inheritDoc
     */
    public function publishedThemePath(): string
    {
        return self::PUBLISHED_THEME_PATH;
    }
}