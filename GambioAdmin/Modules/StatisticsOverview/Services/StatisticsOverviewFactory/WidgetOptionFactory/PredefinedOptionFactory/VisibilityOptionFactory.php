<?php
/*--------------------------------------------------------------
   VisibilityOptionFactory.php 2022-06-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\DropdownOption\OptionItems;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\OptionTitles;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\CheckboxOption;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\NumberOption;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;

/**
 * Class representing a sort order option.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory
 */
class VisibilityOptionFactory
{
    /**
     * Dropdown ID.
     */
    public const ID = "visibility";
    
    /**
     * Default value.
     */
    private const DEFAULT_VALUE = true;
    
    /**
     * German language code.
     */
    private const LANGUAGE_CODE_GERMAN = "de";
    
    /**
     * English language code.
     */
    private const LANGUAGE_CODE_ENGLISH = "en";
    
    /**
     * Multilingual titles.
     */
    private const TITLES = [
        self::LANGUAGE_CODE_GERMAN  => "Sichtbarkeit",
        self::LANGUAGE_CODE_ENGLISH => "Visibility",
    ];
    
    
    /**
     * Return checkbox.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     *
     * @return CheckboxOption Instance.
     */
    public static function create(StatisticsOverviewFactory $factory): CheckboxOption
    {
        return $factory->useOptions()->createCheckbox($factory->useOptions()->createId(self::ID),
                                                      self::DEFAULT_VALUE,
                                                      self::createTitles($factory));
    }
    
    
    /**
     * Return multilingual titles.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     *
     * @return OptionTitles Multilingual titles.
     */
    private static function createTitles(StatisticsOverviewFactory $factory): OptionTitles
    {
        return $factory->useOptions()
            ->createTitles(...array_map(function (string $languageCode, string $title) use ($factory) {
                return $factory->useOptions()->createTitle($factory->createLanguageCode($languageCode), $title);
            },
                array_keys(self::TITLES),
                self::TITLES));
    }
}