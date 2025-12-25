<?php
/*--------------------------------------------------------------
   TimespanOptionFactory.php 2022-05-13
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
 * Class representing a time span dropdown option.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory
 */
class TimespanOptionFactory
{
    /**
     * Dropdown ID.
     */
    public const ID = "timespan";
    
    /**
     * Default value.
     */
    private const DEFAULT_VALUE = "week";
    
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
        self::LANGUAGE_CODE_GERMAN  => "Zeitraum",
        self::LANGUAGE_CODE_ENGLISH => "Time Span",
    ];
    
    /**
     * Dropdown items.
     */
    private const ITEMS = [
        "week"  => [
            self::LANGUAGE_CODE_GERMAN  => "Diese Woche",
            self::LANGUAGE_CODE_ENGLISH => "This week",
        ],
        "month" => [
            self::LANGUAGE_CODE_GERMAN  => "Dieser Monat",
            self::LANGUAGE_CODE_ENGLISH => "This month",
        ],
        "year"  => [
            self::LANGUAGE_CODE_GERMAN  => "Dieses Jahr",
            self::LANGUAGE_CODE_ENGLISH => "This year",
        ],
        "all"   => [
            self::LANGUAGE_CODE_GERMAN  => "Gesamt",
            self::LANGUAGE_CODE_ENGLISH => "Entire period",
        ],
    ];
    
    /**
     * Today dropdown item.
     */
    private const ITEM_TODAY = [
        'today' => [
            self::LANGUAGE_CODE_GERMAN  => "Heute",
            self::LANGUAGE_CODE_ENGLISH => "Today",
        ],
    ];
    
    
    /**
     * Return dropdown including today.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     *
     * @return DropdownOption Instance.
     */
    public static function createIncludingToday(StatisticsOverviewFactory $factory): DropdownOption
    {
        return $factory->useOptions()->useDropdowns()->createDropdown($factory->useOptions()->createId(self::ID),
                                                                      self::DEFAULT_VALUE,
                                                                      self::createItems($factory,
                                                                                        array_merge(self::ITEM_TODAY,
                                                                                                    self::ITEMS)),
                                                                      self::createTitles($factory));
    }
    
    
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
                                                                      self::createItems($factory, self::ITEMS),
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
     * @param array                     $items   Dropdown items.
     *
     * @return OptionItems Dropdown Items.
     */
    private static function createItems(StatisticsOverviewFactory $factory, array $items): OptionItems
    {
        return $factory->useOptions()
            ->useDropdowns()
            ->createItems(...array_map(function (string $value, array $titles) use ($factory) {
                return $factory->useOptions()->useDropdowns()->createItem($factory->useOptions()
                                                                              ->useDropdowns()
                                                                              ->createValue($value),
                                                                          $factory->useOptions()
                                                                              ->useDropdowns()
                                                                              ->createItemTitles(...
                                                                                  array_map(function (
                                                                                      string $languageCode,
                                                                                      string $title
                                                                                  ) use ($factory) {
                                                                                      return $factory->useOptions()
                                                                                          ->useDropdowns()
                                                                                          ->createItemTitle($factory->createLanguageCode($languageCode),
                                                                                                            $title);
                                                                                  },
                                                                                      array_keys($titles),
                                                                                      $titles)));
            },
                array_keys($items),
                $items));
    }
}