<?php
/* --------------------------------------------------------------
  ThemeSettingsFactoryInterface.php 2019-08-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings\Factories\Interfaces;

use Gambio\GX\Services\System\ThemeSettings\ValueObjects\Interfaces\ThemeSettingsInterface;
use stdClass;

/**
 * Interface ThemeSettingsFactoryInterface
 */
interface ThemeSettingsFactoryInterface
{
    /**
     * @param stdClass $jsonObject
     * @param string   $path
     *
     * @return ThemeSettingsInterface
     */
    public function createThemeSettings(stdClass $jsonObject, string $path): ThemeSettingsInterface;
}