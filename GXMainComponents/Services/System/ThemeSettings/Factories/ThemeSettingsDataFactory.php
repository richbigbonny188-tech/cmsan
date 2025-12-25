<?php
/*--------------------------------------------------------------------------------------------------
    ThemeSettingsDataFactory.php 2022-04-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\GX\Services\System\ThemeSettings\Factories;

use ExistingFile;
use FilesystemAdapter;
use Gambio\GX\Services\System\ThemeSettings\Factories\Interfaces\ThemeSettingsDataFactoryInterface;
use Gambio\GX\Services\System\ThemeSettings\ValueObjects\Interfaces\ThemeSettingsDataInterface;
use Gambio\GX\Services\System\ThemeSettings\ValueObjects\ThemeSettingsData;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use MainFactory;
use NonEmptyStringType;

class ThemeSettingsDataFactory extends AbstractThemeSettingsDataFactory
{
    
    /**
     * ThemeSettingsDataFactory constructor.
     *
     * @param FilesystemAdapter|null $filesystem
     * @param string                 $shopSource
     */
    public function __construct(FilesystemAdapter $filesystem = null, string $shopSource = null)
    {
        parent::__construct($filesystem ?? $this->createFileSystem(), $shopSource ?? $this->getShopRoot());
    }

    /**
     * @inheritDoc
     */
    public static function instance(): ThemeSettingsDataFactoryInterface
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return FilesystemAdapter
     */
    protected function createFileSystem(): FilesystemAdapter
    {
        $permissionMap     = [
            'file' => [
                'public'  => 0777,
                'private' => 0700,
            ],
            'dir'  => [
                'public'  => 0777,
                'private' => 0700,
            ],
        ];
        $visibility        = PortableVisibilityConverter::fromArray($permissionMap);
        $filesystemAdapter = new LocalFilesystemAdapter($this->getShopRoot(),
                                                        $visibility,
                                                        LOCK_EX,
                                                        LocalFilesystemAdapter::DISALLOW_LINKS);

        return MainFactory::create(FilesystemAdapter::class, new Filesystem($filesystemAdapter));
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    protected function createThemeSettings(string $filepath): ThemeSettingsDataInterface
    {
        return new ThemeSettingsData(new ExistingFile(new NonEmptyStringType($filepath)));
    }

    /**
     * @return string
     */
    protected function getShopRoot(): string
    {
        return dirname(__DIR__, 5);
    }

}