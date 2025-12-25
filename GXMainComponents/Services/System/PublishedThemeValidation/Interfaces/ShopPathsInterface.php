<?php
/* --------------------------------------------------------------
  ShopPathsInterface.php 2019-12-12
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Interface ShopPathsInterface
 */
interface ShopPathsInterface
{
    /**
     * @return string
     */
    public function webPath(): string;
    
    
    /**
     * @return string
     */
    public function cacheFilePath(): string;
    
    
    /**
     * @return string
     */
    public function publishedThemePath(): string;
}