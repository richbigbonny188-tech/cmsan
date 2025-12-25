<?php
/*--------------------------------------------------------------------------------------------------
    VariantService.php 2020-02-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Variant\Services;

use Exception;
use FileNotFoundException;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;
use Gambio\StyleEdit\Core\Components\Variant\Repositories\VariantSettingsRepository;
use Gambio\StyleEdit\Core\Components\Variant\Repositories\VariantRepository;
use Gambio\StyleEdit\Core\Repositories\Entities\Configuration;
use Gambio\StyleEdit\Core\Repositories\Entities\ConfigurationCollection;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\StyleEditConfiguration;
use stdClass;

/**
 * Class VariantService
 * @package Gambio\StyleEdit\Core\Components\Variant\Services
 */
class VariantService
{

    /**
     * @var VariantRepository
     */
    protected $variantRepository;
    /**
     * @var StyleEditConfiguration
     */
    protected $settings;
    /**
     * @var string
     */
    protected $themeId;

    /**
     * @var VariantSettingsRepository
     */
    protected $variantConfigurationRepository;


    /**
     * VariantService constructor.
     *
     * @param StyleEditConfiguration    $settings
     * @param VariantRepository         $variantRepository
     * @param CurrentThemeInterface     $currentTheme
     *
     * @param VariantSettingsRepository $variantConfigurationRepository
     */
    public function __construct(
        StyleEditConfiguration $settings,
        VariantRepository $variantRepository,
        CurrentThemeInterface $currentTheme = null,
        VariantSettingsRepository $variantConfigurationRepository
    ) {
        $this->settings = $settings;
        $this->variantRepository = $variantRepository;

        if ($currentTheme) {
            $this->themeId = $currentTheme->id();
        }
        $this->variantConfigurationRepository = $variantConfigurationRepository;
    }


    /**
     * @param $themeId
     *
     * @return VariantService
     * @throws Exception
     */
    public static function createForTheme($themeId): VariantService
    {
        /**
         * @var VariantService $result
         */
        $result = SingletonPrototype::instance()->get(__CLASS__);
        $result->setThemeId($themeId);

        return $result;
    }


    /**
     * @param string $themeId
     *
     * @throws Exception
     */
    protected function setThemeId(string $themeId): void
    {
        $this->themeId = $themeId;
        $this->variantRepository = VariantRepository::createForTheme($this->themeId);
    }


    /**
     * @param $variantId
     * @param $optionId
     *
     * @return array|mixed
     */
    public function exists($variantId, $optionId)
    {
        return $this->variantRepository->exists($variantId, $optionId);
    }


    /**
     * @param string $variantId
     * @param string $optionId
     *
     * @return stdClass
     * @throws Exception
     * @throws FileNotFoundException
     */
    public function loadVariantJson(string $variantId, string $optionId): stdClass
    {
        $variantJson = $this->variantRepository->loadVariantJson($variantId, $optionId);
        $variantDir = "variants/{$variantId}/{$optionId}";

        if ($this->variantConfigurationRepository->exists($this->themeId, $variantDir)) {
            $configurationList = $this->variantConfigurationRepository->getAllFrom($this->themeId, $variantDir);
            $originalCount = $configurationList->count();

            // If the fieldset value (from variant.json) does not exists inside settings.json (from variant)
            // we need to add it to the settings.json file
            $this->initializeValuesFromVariantSettingsJson($variantJson, $configurationList);
            $newCount = $configurationList->count();

            if ($originalCount !== $newCount) {
                $this->variantConfigurationRepository->setAllFrom($this->themeId, $variantDir, $configurationList);
            }
        }

        return $variantJson;
    }


    /**
     * @param stdClass $categories
     * @param ConfigurationCollection $configurationCollection
     *
     * @throws Exception
     */
    protected function initializeValuesFromVariantSettingsJson(
        stdClass $categories,
        ConfigurationCollection $configurationCollection
    ): void
    {
        if (isset($categories->categories)) {
            foreach ($categories->categories as $category) {
                $this->initializeValuesFromVariantSettingsJson($category, $configurationCollection);
            }
        } elseif (isset($categories->fieldsets)) {
            $this->initializeValueInFieldSets($categories->fieldsets, $configurationCollection);
        }
    }


    /**
     * @param array $fieldSets
     * @param ConfigurationCollection $configurationCollection
     * @throws Exception
     */
    protected function initializeValueInFieldSets(
        array $fieldSets,
        ConfigurationCollection $configurationCollection
    ): void
    {
        foreach ($fieldSets as $fieldset) {
            foreach ($fieldset->options as $option) {
                if ($configurationCollection && $configurationCollection->keyExists($option->id)) {
                    $option->value = $configurationCollection->getValue($option->id)->value();
                } else {
                    $option->value = $option->default;
                    $option->group = $option->group ?? 'template';
                    $option->name = $option->name ?? $option->id;

                    // Add the option to collection if it isn't an object (groups are objects inside variant.json)
                    if (!is_object($option->value)) {
                        $configurationCollection->addItem(Configuration::createFromJson($option));
                    }
                }
            }
        }
    }


    /**
     * @param string|null $variantId
     * @param string $optionId
     *
     * @return ConfigurationCollection
     * @throws Exception
     */
    public function getSettingsFromVariant(?string $variantId, string $optionId): ConfigurationCollection
    {
        $dir = implode(DIRECTORY_SEPARATOR, ['variants', $variantId, $optionId]);
        return $this->variantConfigurationRepository->getAllFrom($this->themeId, $dir);
    }


    /**
     * @param string $variantDirectory
     *
     * @return array|mixed
     * @throws Exception
     */
    public function hasSettings(string $variantDirectory): bool
    {
        return $this->variantConfigurationRepository->exists($this->themeId, $variantDirectory);
    }

    /**
     * @param string $variantId
     * @param string $optionId
     */
    public function createInheritedVariantOption(string $variantId, string $optionId): void
    {
        $this->variantRepository->createInheritedVariantOption($variantId, $optionId);
    }
}