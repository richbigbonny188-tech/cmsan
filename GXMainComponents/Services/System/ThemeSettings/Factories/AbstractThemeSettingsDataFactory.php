<?php
/*--------------------------------------------------------------------------------------------------
    AbstractThemeSettingsDataFactory.php 2023-03-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\GX\Services\System\ThemeSettings\Factories;

use ExistingDirectory;
use FilesystemAdapter;
use Gambio\GX\Services\System\ThemeSettings\Factories\Interfaces\ThemeSettingsDataFactoryInterface;
use Gambio\GX\Services\System\ThemeSettings\ValueObjects\Interfaces\ThemeSettingsDataInterface;

abstract class AbstractThemeSettingsDataFactory implements ThemeSettingsDataFactoryInterface
{
    /**
     * @var AbstractThemeSettingsDataFactory
     */
    protected static $instance;
    /**
     * @var FilesystemAdapter
     */
    protected $filesystem;
    /**
     * @var string
     */
    protected $shopSource;


    /**
     * AbstractThemeSettingsDataFactory constructor.
     *
     * @param FilesystemAdapter $filesystem
     * @param string $shopSource
     */
    public function __construct(FilesystemAdapter $filesystem, string $shopSource)
    {

        $this->filesystem = $filesystem;
        $this->shopSource = $shopSource;
    }


    /**
     * @inheritDoc
     */
    public function createForDirectory(ExistingDirectory $directory): ?ThemeSettingsDataInterface
    {
        return $this->_createForRelativeDirectory($this->relativizeDir($directory));
    }

    /**
     * @inheritDoc
     */
    public function createForTheme(string $themeId): ?ThemeSettingsDataInterface
    {
        $dir = "/themes/$themeId/";

        return $this->_createForRelativeDirectory($dir);
    }

    /**
     * @param string $relativeDirectory
     *
     * @return ThemeSettingsDataInterface|null
     */
    protected function _createForRelativeDirectory(string $relativeDirectory): ?ThemeSettingsDataInterface
    {

        if ($this->filesystem->has($relativeDirectory . 'settings.json')) {
            return $this->createThemeSettings($this->shopSource . $relativeDirectory . 'settings.json');
        }

        if ($this->filesystem->has($relativeDirectory . 'settings.default.json')) {
            return $this->createThemeSettings($this->shopSource . $relativeDirectory . 'settings.default.json');
        }

        return null;
    }

    /**
     * @param string $filepath
     *
     * @return mixed
     */
    abstract protected function createThemeSettings(string $filepath): ThemeSettingsDataInterface;

    /**
     * @param ExistingDirectory $directory
     *
     * @return string
     */
    protected function relativizeDir(ExistingDirectory $directory): string
    {
        return str_replace($this->shopSource, '', $directory->getDirPath()) . DIRECTORY_SEPARATOR;
    }


}