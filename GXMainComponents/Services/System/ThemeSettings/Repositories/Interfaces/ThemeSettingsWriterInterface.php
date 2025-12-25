<?php
/* --------------------------------------------------------------
  ThemeSettingsWriterInterface.php 2019-08-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces;

use Gambio\GX\Services\System\ThemeSettings\ValueObjects\Interfaces\ThemeSettingsInterface;

/**
 * Interface ThemeSettingsWriterInterface
 */
interface ThemeSettingsWriterInterface
{
    /**
     * @param ThemeSettingsInterface $settings
     */
    public function updateThemeSetting(ThemeSettingsInterface $settings): void;
    
    
    /**
     * @param ThemeSettingsInterface $settings
     */
    public function updateDatabaseEntry(ThemeSettingsInterface $settings): void;
}