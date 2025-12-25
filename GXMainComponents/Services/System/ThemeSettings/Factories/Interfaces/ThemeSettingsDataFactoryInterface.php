<?php
/*--------------------------------------------------------------------------------------------------
    ThemeSettingsDataFactoryInterface.php 2020-02-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\GX\Services\System\ThemeSettings\Factories\Interfaces;

use ExistingDirectory;
use Gambio\GX\Services\System\ThemeSettings\ValueObjects\Interfaces\ThemeSettingsDataInterface;

interface ThemeSettingsDataFactoryInterface
{

    /**
     * @param ExistingDirectory $directory
     *
     * @return ThemeSettingsDataInterface|null
     */
    public function createForDirectory(ExistingDirectory $directory): ?ThemeSettingsDataInterface;

    /**
     * @param string $themeId
     *
     * @return ThemeSettingsDataInterface|null
     */
    public function createForTheme(string $themeId): ?ThemeSettingsDataInterface;

    /**
     * @return ThemeSettingsDataFactoryInterface
     */
    public static function instance(): ThemeSettingsDataFactoryInterface;
}