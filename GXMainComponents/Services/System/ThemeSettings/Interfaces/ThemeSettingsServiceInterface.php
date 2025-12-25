<?php
/* --------------------------------------------------------------
  ThemeSettingsServiceInterface.php 2019-09-02
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings\Interfaces;

/**
 * Interface ThemeSettingsServiceInterface
 */
interface ThemeSettingsServiceInterface
{
    /**
     * @param string $themeId
     * @param bool   $clearCache
     */
    public function activateTheme(string $themeId, bool $clearCache): void;
}