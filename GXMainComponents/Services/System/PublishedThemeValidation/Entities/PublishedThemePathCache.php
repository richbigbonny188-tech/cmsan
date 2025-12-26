<?php
/* --------------------------------------------------------------
  PublishedThemePathCache.php 2023-03-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class PublishedThemePathCache
 */
class PublishedThemePathCache implements PublishedThemePathCacheInterface
{
    /**
     * @var string
     */
    protected $path;
    
    
    /**
     * PublishedThemePathCache constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }
    
    
    /**
     * @inheritDoc
     */
    public function path(): string
    {
        return $this->path;
    }
}