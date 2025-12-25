<?php
/* --------------------------------------------------------------
  PublishedThemePathCacheInterface.php 2019-12-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Interface PublishedThemePathCacheInterface
 */
interface PublishedThemePathCacheInterface
{
    /**
     * @return string
     */
    public function path(): string;
}