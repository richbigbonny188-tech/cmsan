<?php
/*--------------------------------------------------------------
   WidgetOptionFactory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;

use Exception;
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageCode;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\OptionTitles;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\CheckboxOption;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\NumberOption;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\OptionId;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\OptionTitle;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\OptionUpdateSet;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\TextOption;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\DropdownOptionFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory;
use InvalidArgumentException;

/**
 * Class representing widget option factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory
 */
class WidgetOptionFactory
{
    /**
     * Dropdown factory.
     *
     * @var DropdownOptionFactory
     */
    private $dropdownFactory;
    
    /**
     * Predefined option factory.
     *
     * @var PredefinedOptionFactory
     */
    private $predefinedFactory;
    
    
    /**
     * Constructor.
     *
     * @param DropdownOptionFactory   $dropdownFactory   Dropdown factory.
     * @param PredefinedOptionFactory $predefinedFactory Predefined option factory.
     */
    public function __construct(
        DropdownOptionFactory $dropdownFactory,
        PredefinedOptionFactory $predefinedFactory
    ) {
        $this->dropdownFactory   = $dropdownFactory;
        $this->predefinedFactory = $predefinedFactory;
    }
    
    
    /**
     * Return dropdown factory.
     *
     * @return DropdownOptionFactory Dropdown factory.
     */
    public function useDropdowns(): DropdownOptionFactory
    {
        return $this->dropdownFactory;
    }
    
    
    /**
     * Return predefined option factory.
     *
     * @return PredefinedOptionFactory Predefined option factory.
     */
    public function usePredefined(): PredefinedOptionFactory
    {
        return $this->predefinedFactory;
    }
    
    
    /**
     * Return options.
     *
     * @param WidgetOption ...$options Options.
     *
     * @return WidgetOptions Options.
     */
    public function createOptions(WidgetOption ...$options): WidgetOptions
    {
        return WidgetOptions::create(...$options);
    }
    
    
    /**
     * Return title.
     *
     * @param LanguageCode $languageCode Language code.
     * @param string       $title        Title.
     *
     * @return OptionTitle
     */
    public function createTitle(LanguageCode $languageCode, string $title): OptionTitle
    {
        return OptionTitle::create($languageCode, $title);
    }
    
    
    /**
     * Return multilingual titles.
     *
     * @param OptionTitle ...$titles Multilingual Titles.
     *
     * @return OptionTitles Multilingual Titles.
     */
    public function createTitles(OptionTitle ...$titles): OptionTitles
    {
        return OptionTitles::create(...$titles);
    }
    
    
    /**
     * Return number option.
     *
     * @param OptionId     $id     ID.
     * @param int          $value  Value.
     * @param OptionTitles $titles Multilingual titles.
     *
     * @return NumberOption Number option.
     */
    public function createNumber(OptionId $id, int $value, OptionTitles $titles): NumberOption
    {
        return NumberOption::create($id, $value, $titles);
    }
    
    
    /**
     * Return text option.
     *
     * @param OptionId     $id     ID.
     * @param string       $value  Value.
     * @param OptionTitles $titles Multilingual titles.
     *
     * @return TextOption Text option.
     */
    public function createText(OptionId $id, string $value, OptionTitles $titles): TextOption
    {
        return TextOption::create($id, $value, $titles);
    }
    
    
    /**
     * Return checkbox option.
     *
     * @param OptionId     $id     ID.
     * @param bool         $value  Value.
     * @param OptionTitles $titles Multilingual titles.
     *
     * @return CheckboxOption Checkbox option.
     */
    public function createCheckbox(OptionId $id, bool $value, OptionTitles $titles): CheckboxOption
    {
        return CheckboxOption::create($id, $value, $titles);
    }
    
    
    /**
     * Return update set.
     *
     * @param array $updateSet Update set.
     *
     * @return OptionUpdateSet Update set.
     */
    public function createUpdateSet(array $updateSet): OptionUpdateSet
    {
        return OptionUpdateSet::create($updateSet);
    }
    
    
    /**
     * Return ID.
     *
     * @param string $id ID.
     *
     * @return OptionId ID.
     */
    public function createId(string $id): OptionId
    {
        return OptionId::create($id);
    }
    
    
    /**
     * Return options from update set and widget options.
     *
     * @param OptionUpdateSet $updateSet Update set.
     * @param WidgetOptions   $options   Widget options.
     *
     * @return WidgetOptions Widget options.
     * @throws Exception When option has not been found.
     */
    public function createOptionsFromUpdateSet(
        OptionUpdateSet $updateSet,
        WidgetOptions $options
    ): WidgetOptions {
        return $this->createOptions(...array_map(function (WidgetOption $option) use ($updateSet) {
            $id     = $option->id();
            $value  = $updateSet->toArray()[$id->value()] ?? $option->value();
            $titles = $option->titles();
            
            if ($option instanceof CheckboxOption) {
                return $this->createCheckbox($id, $value, $titles);
            }
            
            if ($option instanceof DropdownOption) {
                return $this->useDropdowns()->createDropdown($id, $value, $option->items(), $titles);
            }
            
            if ($option instanceof NumberOption) {
                return $this->createNumber($id, $value, $titles);
            }
            
            if ($option instanceof TextOption) {
                return $this->createText($id, $value, $titles);
            }
            
            throw new InvalidArgumentException("Option not found");
        },
            iterator_to_array($options->getIterator())));
    }
}