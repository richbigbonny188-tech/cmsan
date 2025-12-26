<?php
/* --------------------------------------------------------------
  ThemeSettingsFactory.php 2019-08-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings\Factories;

use Gambio\GX\Services\System\ThemeSettings\Factories\Interfaces\ThemeSettingsFactoryInterface;
use Gambio\GX\Services\System\ThemeSettings\ValueObjects\Interfaces\ThemeSettingsInterface;
use Gambio\GX\Services\System\ThemeSettings\ValueObjects\ThemeSettings;
use stdClass;

/**
 * Class ThemeSettingsFactory
 */
class ThemeSettingsFactory implements ThemeSettingsFactoryInterface
{
    /**
     * @param stdClass $jsonObject
     * @param string   $path
     *
     * @return ThemeSettingsInterface
     */
    public function createThemeSettings(stdClass $jsonObject, string $path): ThemeSettingsInterface
    {
        return ThemeSettings::createFromJsonObject($jsonObject, $path);
    }
}