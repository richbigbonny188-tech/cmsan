<?php
/* --------------------------------------------------------------
  VariantRepository.php 2019-10-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\Variant\Repositories;

use Exception;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;
use Gambio\StyleEdit\Core\Components\Variant\Json\VariantInheritanceHandler;
use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\StyleEditConfiguration;
use RuntimeException;
use stdClass;

/**
 * Class VariantRepository
 *
 * @package Gambio\StyleEdit\Core\Components\Variant\Repositories
 */
class VariantRepository
{
    /**
     * @var StyleEditConfiguration
     */
    protected $configuration;
    /**
     * @var FileIO
     */
    protected $fileIO;
    /**
     * @var CurrentThemeInterface
     */
    protected $themeId;


    /**
     * VariantRepository constructor.
     *
     * @param StyleEditConfiguration $configuration
     * @param FileIO $fileIO
     * @param CurrentThemeInterface $currentTheme
     */
    public function __construct(
        StyleEditConfiguration $configuration,
        FileIO $fileIO,
        CurrentThemeInterface $currentTheme = null
    ) {
        $this->configuration = $configuration;
        $this->fileIO = $fileIO;
        if ($currentTheme) {
            $this->themeId = $currentTheme->id();
        }
    }


    /**
     * @param string $themeId
     *
     * @return VariantRepository
     * @throws Exception
     */
    public static function createForTheme(string $themeId)
    {
        /**
         * @var VariantRepository $result
         */
        $result = SingletonPrototype::instance()->get(VariantRepository::class);
        $result->setThemeId($themeId);

        return $result;
    }


    /**
     * @param string $themeId
     */
    protected function setThemeId(string $themeId): void
    {
        $this->themeId = $themeId;
    }


    /**
     * @param $variantId
     * @param $optionId
     *
     * @return array|mixed
     */
    public function exists($variantId, $optionId)
    {
        $directory = $this->getVariantDirectory($variantId, $optionId);
        $variantFilename = $this->getVariantFilename($directory);

        return $this->fileIO->exists($variantFilename);
    }


    /**
     * @param $directory
     *
     * @return string
     */
    protected function getVariantFilename($directory): string
    {
        return implode(DIRECTORY_SEPARATOR,
            [
                $this->getVariantAbsolutePath($directory),
                'variant.json'
            ]);
    }


    /**
     * @param string $variantId
     * @param string $optionId
     *
     * @return stdClass
     *
     * @throws \FileNotFoundException
     */
    public function loadVariantJson(string $variantId, string $optionId): stdClass
    {
        $variantsDir = $this->getVariantDirectory($variantId, $optionId);
        $variantFilename = $this->getVariantFilename($variantsDir);

        if (!$this->fileIO->exists($variantFilename)) {
            throw new RuntimeException($variantFilename . ' does not exist');
        }

        /**
         * @var VariantInheritanceHandler $handler
         */
        $handler = SingletonPrototype::instance()->get(VariantInheritanceHandler::class);
        $handler->setFilename($variantFilename);
        $variantJson = $handler->execute();

        return $variantJson;
    }


    /**
     * @param string $directory relative path to variant option
     *
     * @return bool
     * @throws Exception
     */
    public function variantJsonExists(string $directory): bool
    {
        return $this->fileIO->exists($this->getVariantFilename($directory));
    }


    /**
     * @param string $directory
     *
     * @return array|mixed
     */
    public function hasSettings(string $directory)
    {
        return $this->fileIO->exists($this->getVariantSettingsFilename($directory));
    }


    /**
     * @param $directory
     *
     * @return string
     */
    protected function getVariantSettingsFilename($directory): string
    {
        return implode(DIRECTORY_SEPARATOR,
            [
                $this->getVariantAbsolutePath($directory),
                'settings.json'
            ]);
    }

    /**
     * @param string $directory
     *
     * @return string
     */
    protected function getVariantAbsolutePath(string $directory): string
    {
        return implode(DIRECTORY_SEPARATOR,
            [
                rtrim($this->configuration->themesFolderPath(), DIRECTORY_SEPARATOR),
                $this->themeId,
                $directory
            ]);
    }


    /**
     * @param $variantId
     * @param $optionId
     *
     * @return string
     */
    protected function getVariantDirectory($variantId, $optionId): string
    {
        return "variants/$variantId/$optionId";
    }


    /**
     * @param string $variantId
     * @param string $optionId
     */
    public function createInheritedVariantOption(string $variantId, string $optionId): void
    {
        $variantDir = $this->getVariantDirectory($variantId, $optionId);
        $dir = $this->getVariantFilename($variantDir);

        if (!$this->fileIO->exists($dir)) {
            $this->fileIO->createDirectory(str_replace('/variant.json', '', $dir));
        }

        $variantJson = (object)[
            'type' => 'category',
            'inherits' => 'PARENT'
        ];

        $this->fileIO->write($variantJson, $dir);
    }
}
