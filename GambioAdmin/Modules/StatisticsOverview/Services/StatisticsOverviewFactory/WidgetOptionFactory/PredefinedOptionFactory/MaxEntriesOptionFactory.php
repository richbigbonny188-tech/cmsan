<?php
/*--------------------------------------------------------------
   MaxEntriesOptionFactory.php 2022-05-13
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
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;

/**
 * Class representing a maximum entries dropdown option.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory
 */
class MaxEntriesOptionFactory
{
    /**
     * Dropdown ID.
     */
    public const ID = "maxEntries";
    
    /**
     * Default value.
     */
    private const DEFAULT_VALUE = "5";
    
    /**
     * Dropdown values.
     */
    private const ITEMS = ["5", "10", "20"];
    
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
        self::LANGUAGE_CODE_GERMAN  => "Max. Einträge",
        self::LANGUAGE_CODE_ENGLISH => "Max. Entries"
    ];
    
    
    /**
     * Return dropdown.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     *
     * @return DropdownOption Instance.
     */
    public static function create(StatisticsOverviewFactory $factory): DropdownOption
    {
        return $factory->useOptions()->useDropdowns()->createDropdown($factory->useOptions()->createId(self::ID),
                                                                      self::DEFAULT_VALUE,
                                                                      self::createItems($factory),
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
    
    
    /**
     * Return dropdown items.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     *
     * @return OptionItems Dropdown Items.
     */
    private static function createItems(StatisticsOverviewFactory $factory): OptionItems
    {
        return $factory->useOptions()->useDropdowns()->createItems(...array_map(function (string $item) use ($factory) {
            return $factory->useOptions()->useDropdowns()->createItem($factory->useOptions()
                                                                          ->useDropdowns()
                                                                          ->createValue($item),
                                                                      $factory->useOptions()
                                                                          ->useDropdowns()
                                                                          ->createItemTitles($factory->useOptions()
                                                                                                 ->useDropdowns()
                                                                                                 ->createItemTitle($factory->createLanguageCode(self::LANGUAGE_CODE_GERMAN),
                                                                                                                   $item),
                                                                                             $factory->useOptions()
                                                                                                 ->useDropdowns()
                                                                                                 ->createItemTitle($factory->createLanguageCode(self::LANGUAGE_CODE_ENGLISH),
                                                                                                                   $item)));
        },
            self::ITEMS));
    }
}