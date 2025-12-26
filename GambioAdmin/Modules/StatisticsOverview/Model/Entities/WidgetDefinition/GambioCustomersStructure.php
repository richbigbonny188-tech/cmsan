<?php
/* --------------------------------------------------------------
  GambioCustomersStructure.php 2023-06-09
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class representing a widget showing the customer's demographic.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioCustomersStructure extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioCustomersStructure';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Kundenstruktur',
        self::LANGUAGE_CODE_ENGLISH => 'Customers Structure',
    ];
    
    /**
     * Multilingual categories.
     */
    private const CATEGORIES = [
        self::LANGUAGE_CODE_GERMAN  => [
            '> 90',
            '81-90',
            '71-80',
            '61-70',
            '51-60',
            '41-50',
            '31-40',
            '21-30',
            '18-20',
            '< 18',
            'Nicht angegeben',
        ],
        self::LANGUAGE_CODE_ENGLISH => [
            '> 90',
            '81-90',
            '71-80',
            '61-70',
            '51-60',
            '41-50',
            '31-40',
            '21-30',
            '18-20',
            '< 18',
            'Not specified',
        ],
    ];
    
    /**
     * Multilingual item titles.
     */
    private const ITEM_TITLES = [
        'left'  => [
            self::LANGUAGE_CODE_GERMAN  => 'Frauen',
            self::LANGUAGE_CODE_ENGLISH => 'Females',
        ],
        'right' => [
            self::LANGUAGE_CODE_GERMAN  => 'Männer',
            self::LANGUAGE_CODE_ENGLISH => 'Males',
        ],
    ];
    
    /**
     * Factory.
     *
     * @var StatisticsOverviewFactory
     */
    private $factory;
    
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;
    
    /**
     * User's preferences.
     *
     * @var UserPreferences
     */
    private $userPreferences;
    
    
    /**
     * @inheritDoc
     */
    public function __construct(
        StatisticsOverviewFactory $factory,
        UserPreferences           $userPreferences,
        Connection                $connection
    ) {
        $this->factory         = $factory;
        $this->connection      = $connection;
        $this->userPreferences = $userPreferences;
        
        parent::__construct($factory->createId(self::ID),
                            $factory->createNames($factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_GERMAN),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_GERMAN]),
                                                  $factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_ENGLISH),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_ENGLISH])),
                            $factory->useCategories()->createForCustomers(),
                            $factory->useVisualizations()->createTwoSidedBarChart(),
                            $factory->useOptions()->createOptions($factory->useOptions()
                                                                      ->usePredefined()
                                                                      ->createSortOrderNumber($factory),
                                                                  $factory->useOptions()
                                                                      ->usePredefined()
                                                                      ->createVisibilityCheckbox($factory)));
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function data(WidgetOptions $options): WidgetData
    {
        $ageRanges = [121, 91, 81, 71, 61, 51, 41, 31, 21, 18, 0];
        
        $serialized = [
            'category' => [],
            'left'     => [],
            'right'    => [],
        ];
        
        $customersDemographic = $this->getSumOfMaleAndFemaleCustomersGroupedByAgeFromDatabase();
        
        $groupedValues = $this->groupDemographicByAgeRanges($customersDemographic, $ageRanges);
        
        foreach (self::CATEGORIES[$this->userPreferences->languageId()
                                  == self::LANGUAGE_ID_GERMAN ? self::LANGUAGE_CODE_GERMAN : self::LANGUAGE_CODE_ENGLISH] as $category) {
            $serialized['category'][] = $this->factory->useData()->useSerialData()->createCategory($category);
        }
        
        foreach ($groupedValues['left'] as $value) {
            $serialized['left'][] = $this->factory->useData()->useSerialData()->createItemValue((int)$value);
        }
        foreach ($groupedValues['right'] as $value) {
            $serialized['right'][] = $this->factory->useData()->useSerialData()->createItemValue((int)$value);
        }
        
        $itemTitleLeft  = self::ITEM_TITLES['left'][$this->userPreferences->languageId()
                                                    == self::LANGUAGE_ID_GERMAN ? self::LANGUAGE_CODE_GERMAN : self::LANGUAGE_CODE_ENGLISH];
        $itemTitleRight = self::ITEM_TITLES['right'][$this->userPreferences->languageId()
                                                     == self::LANGUAGE_ID_GERMAN ? self::LANGUAGE_CODE_GERMAN : self::LANGUAGE_CODE_ENGLISH];
        
        return $this->factory->useData()->useSerialData()->createSerialData($this->factory->useData()
                                                                                ->useSerialData()
                                                                                ->createCategories(...
                                                                                    $serialized['category']),
                                                                            $this->factory->useData()
                                                                                ->useSerialData()
                                                                                ->createItems($this->factory->useData()
                                                                                                  ->useSerialData()
                                                                                                  ->createItem($this->factory->useData()
                                                                                                                   ->useSerialData()
                                                                                                                   ->createItemTitle($itemTitleLeft),
                                                                                                               $this->factory->useData()
                                                                                                                   ->useSerialData()
                                                                                                                   ->createItemValues(...
                                                                                                                       $serialized['left'])),
                                                                                              $this->factory->useData()
                                                                                                  ->useSerialData()
                                                                                                  ->createItem($this->factory->useData()
                                                                                                                   ->useSerialData()
                                                                                                                   ->createItemTitle($itemTitleRight),
                                                                                                               $this->factory->useData()
                                                                                                                   ->useSerialData()
                                                                                                                   ->createItemValues(...
                                                                                                                       $serialized['right']))));
    }
    
    
    /**
     * Fetch customers data from database.
     *
     * @return array Sum of customers per gender.
     * @throws Exception
     */
    private function getSumOfMaleAndFemaleCustomersGroupedByAgeFromDatabase(): array
    {
        return $this->connection->createQueryBuilder()
            ->select([
                         "SUM(if(customers_gender = 'f', 1, 0)) AS females",
                         "SUM(if(customers_gender = 'm', 1, 0)) AS males",
                         "TIMESTAMPDIFF(YEAR, customers_dob, CURDATE()) AS age",
                     ])
            ->from('customers')
            ->where('customers_status != 0 && customers_status != 1')
            ->groupBy('age')
            ->orderBy('age', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * Group customers by age groups.
     *
     * @param array $customersDemographic Customer's demographic.
     * @param array $ageRanges            Age ranges.
     *
     * @return array[] Customers grouped by ages.
     */
    private function groupDemographicByAgeRanges(array $customersDemographic, array $ageRanges): array
    {
        $ageGroups    = [];
        $femaleValues = [];
        $maleValues   = [];
        
        $undefinedValues = [
            'left'  => [],
            'right' => [],
        ];
        
        $ageRangesLastIndex = count($ageRanges) - 1;
        $groupedValuesLeft  = [];
        $groupedValuesRight = [];
        
        foreach ($customersDemographic as $data) {
            $femaleValues[] = (int)$data['females'];
            $maleValues[]   = (int)$data['males'];
            $ageGroups[]    = (int)$data['age'];
        }
        
        for ($i = 0; $i < $ageRangesLastIndex; $i++) {
            $groupedValuesLeft[]  += 0;
            $groupedValuesRight[] += 0;
            for ($j = 0; !empty($ageGroups[$j])
                         && $this->ageGroupIsBiggerOrEqualToLowerEndOfAgeRange($ageGroups[$j],
                                                                               $ageRanges[$i + 1]); $j++) {
                if ($this->ageGroupIsBiggerThanUpperEndOfAgeRange($ageGroups[$j], $ageRanges[0])) {
                    $undefinedValues['left']  = array_shift($femaleValues);
                    $undefinedValues['right'] = array_shift($maleValues);
                } else {
                    $groupedValuesLeft[$i]  += array_shift($femaleValues);
                    $groupedValuesRight[$i] += array_shift($maleValues);
                }
                unset($ageGroups[$j]);
                $ageGroups = array_values($ageGroups);
                $j--;
            }
        }
        
        $groupedValuesLeft[$ageRangesLastIndex]  = $undefinedValues['left'] ?? 0;
        $groupedValuesRight[$ageRangesLastIndex] = $undefinedValues['right'] ?? 0;
        
        return [
            'left'  => $groupedValuesLeft,
            'right' => $groupedValuesRight,
        ];
    }
    
    
    /**
     * Return whether the age group is bigger or equal to a given age.
     *
     * @param int $currentAgeGroup         Current age group.
     * @param int $currentAgeRangeLowerEnd Lower end of current age range.
     *
     * @return bool Result.
     */
    private function ageGroupIsBiggerOrEqualToLowerEndOfAgeRange(
        int $currentAgeGroup,
        int $currentAgeRangeLowerEnd
    ): bool {
        return $currentAgeGroup >= $currentAgeRangeLowerEnd;
    }
    
    
    /**
     * Return whether the age group is bigger than the biggest range group.
     *
     * @param int $currentAgeGroup         Current age group.
     * @param int $currentAgeRangeUpperEnd Upper end of current age range.
     *
     * @return bool Result.
     */
    private function ageGroupIsBiggerThanUpperEndOfAgeRange(int $currentAgeGroup, int $currentAgeRangeUpperEnd): bool
    {
        return $currentAgeGroup >= $currentAgeRangeUpperEnd;
    }
}