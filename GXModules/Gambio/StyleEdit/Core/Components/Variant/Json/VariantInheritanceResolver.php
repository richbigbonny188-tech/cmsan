<?php
/*--------------------------------------------------------------------------------------------------
    VariantInheritanceResolver.php 2019-10-15
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Variant\Json;

use Exception;
use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\Core\Json\InheritanceHandler;
use Gambio\StyleEdit\Core\Json\Interfaces\InheritanceResolverInterface;

/**
 * Class VariantExtendsPropertyResolver
 * @package Gambio\StyleEdit\Core\Json
 */
class VariantInheritanceResolver implements InheritanceResolverInterface
{
    public const SELF = 'SELF';
    public const PARENT = 'PARENT';
    /**
     * @var FileIO
     */
    protected $fileIO;


    /**
     * VariantExtendsPropertyResolver constructor.
     *
     * @param FileIO $fileIO
     */
    public function __construct(FileIO $fileIO)
    {
        $this->fileIO = $fileIO;
    }


    /**
     * @param $settings
     *
     * @param $sourceFilename
     *
     * @return string
     * @throws Exception
     */
    public function resolveInheritanceFileName($settings, $sourceFilename): string
    {
        $variantSettings = ResolverVariantSettings::createFromSettingsAndPath($settings, $sourceFilename);

        $parentTheme = $this->getFirstParentWithVariant($variantSettings);

        if (!$parentTheme) {
            $variant = implode(DIRECTORY_SEPARATOR,
                [
                    $variantSettings->theme(),
                    'variants',
                    $variantSettings->variant(),
                    $variantSettings->variantOption()
                ]);
            throw new Exception("There is no parent variant for variant '$variant'");
        }

        return $this->getVariantPathFromThemeAndSettings($parentTheme, $variantSettings);
    }


    /**
     * @param ResolverVariantSettings $variantSettings
     *
     * @return string|null
     * @throws Exception
     */
    protected function getFirstParentWithVariant(ResolverVariantSettings $variantSettings): ?string
    {
        $parentTheme = $variantSettings->parentTheme();
        $theme = $variantSettings->theme();

        do {
            $theme = $this->lookupThemeId(
                $parentTheme,
                $theme,
                $variantSettings->basePath()
            );
            $variantPath = $this->getVariantPathFromThemeAndSettings($theme, $variantSettings);
            $parentTheme = self::PARENT;
        } while (!$this->fileIO->exists($variantPath) && $theme !== null);

        return $theme;
    }


    /**
     * @param ResolverVariantSettings $variantSettings
     * @param $resourcePath
     * @return string|null
     * @throws Exception
     */
    protected function getFirstParentWithResource(ResolverVariantSettings $variantSettings, $resourcePath): ?string
    {
        $parentTheme = $variantSettings->parentTheme();
        $theme = $variantSettings->theme();

        do {
            $theme = $this->lookupThemeId(
                $parentTheme,
                $theme,
                $variantSettings->basePath()
            );
            $resourceFullPath = $this->getResourceFullPath($theme, $variantSettings, $resourcePath);
            $parentTheme = self::PARENT;
        } while (!$this->fileIO->exists($resourceFullPath) && $theme !== null);

        return $theme;
    }


    /**
     * @param                         $theme
     * @param ResolverVariantSettings $variantSettings
     *
     * @return string
     */
    public function getVariantPathFromThemeAndSettings($theme, ResolverVariantSettings $variantSettings): string
    {
        return implode(DIRECTORY_SEPARATOR,
            [
                $variantSettings->basePath(),
                $theme,
                'variants',
                $variantSettings->parentId(),
                'variant.json'
            ]);
    }


    public function getResourceFullPath($theme, ResolverVariantSettings $variantSettings, string $resourcePath): string
    {
        return implode(DIRECTORY_SEPARATOR,
            [
                $variantSettings->basePath(),
                $theme,
                'variants',
                $variantSettings->parentId(),
                $resourcePath
            ]);
    }

    /**
     * @param string $parentTheme
     * @param string $theme
     * @param string $themeBasePath
     *
     * @return string
     * @throws Exception
     */
    protected function lookupThemeId(string $parentTheme, string $theme, string $themeBasePath): ?string
    {
        if ($parentTheme === self::PARENT) {
            return $this->getParentThemeIdOf($themeBasePath, $theme);
        } elseif ($parentTheme === self::SELF) {
            return $theme;
        } else {
            return $parentTheme;
        }
    }


    /**
     * @param string $themeBasePath
     * @param string $theme
     *
     * @return string|null
     * @throws Exception
     */
    protected function getParentThemeIdOf(string $themeBasePath, string $theme): ?string
    {
        $filename = implode(DIRECTORY_SEPARATOR, [$themeBasePath, $theme, 'theme.json']);
        if ($this->fileIO->exists($filename)) {
            $object = $this->fileIO->read($filename);

            return $object->extends ?? null;
        }

        return null;
    }


    /**
     * @param $settings
     *
     * @param $sourceFilename
     *
     * @return string
     */
    public function resolveInheritanceType($settings, $sourceFilename): string
    {
        return is_object($settings) ? ($settings->type ?? InheritanceHandler::MERGE) : InheritanceHandler::MERGE;
    }


    /**
     * @return string
     */
    public function jsonExtensionFolder(): string
    {
        return 'variant_extensions/';
    }

    /**
     * @param $settings
     * @param $resourcePath
     * @param $sourceFilename
     *
     * @return mixed|string|null
     *
     * @throws Exception
     */
    public function getResourceRelativePath($settings, $resourcePath, $sourceFilename)
    {
        $variantSettings = ResolverVariantSettings::createFromSettingsAndPath($settings, $resourcePath);
        $path = $this->getResourceFullPath($variantSettings->theme(), $variantSettings, $sourceFilename);

        if (!$this->fileIO->exists($path)) {
            $theme = $this->getFirstParentWithResource($variantSettings, $sourceFilename);
            $path = $this->getResourceFullPath($theme, $variantSettings, $sourceFilename);
        }

        return rtrim(
            str_replace($variantSettings->basePath(), '', $path),
            DIRECTORY_SEPARATOR
        );
    }
}