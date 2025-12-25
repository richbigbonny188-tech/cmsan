<?php
/* --------------------------------------------------------------
   Option.php 2021-08-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model;

use Gambio\Admin\Modules\Option\Model\Collections\NewOptionValues;
use Gambio\Admin\Modules\Option\Model\Collections\OptionDetails;
use Gambio\Admin\Modules\Option\Model\Collections\OptionValues;
use Gambio\Admin\Modules\Option\Model\Entities\OptionValue;
use Gambio\Admin\Modules\Option\Model\Events\NewOptionValueAdded;
use Gambio\Admin\Modules\Option\Model\Events\OptionDetailsUpdated;
use Gambio\Admin\Modules\Option\Model\Events\OptionsSortOrderUpdated;
use Gambio\Admin\Modules\Option\Model\Events\OptionsTypeUpdated;
use Gambio\Admin\Modules\Option\Model\Events\OptionValueDeleted;
use Gambio\Admin\Modules\Option\Model\Events\OptionValueUpdated;
use Gambio\Admin\Modules\Option\Model\ValueObjects\NewOptionValue;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionType;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueId;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;
use Webmozart\Assert\Assert;

/**
 * Class Option
 *
 * @package Gambio\Admin\Modules\Option\Model
 */
class Option extends AbstractEventRaisingEntity
{
    /**
     * @var OptionId
     */
    private $id;
    
    /**
     * @var OptionDetails
     */
    private $details;
    
    /**
     * @var OptionValues
     */
    private $values;
    
    /**
     * @var NewOptionValues
     */
    private $newValues;
    
    /**
     * @var OptionType
     */
    private $type;
    
    /**
     * @var int
     */
    private $sortOrder;
    
    
    /**
     * Option constructor.
     *
     * @param OptionId        $id
     * @param OptionDetails   $details
     * @param OptionValues    $values
     * @param NewOptionValues $newValues
     * @param OptionType      $type
     * @param int             $sortOrder
     */
    private function __construct(
        OptionId $id,
        OptionDetails $details,
        OptionValues $values,
        NewOptionValues $newValues,
        OptionType $type,
        int $sortOrder
    ) {
        $this->id        = $id;
        $this->details   = $details;
        $this->values    = $values;
        $this->newValues = $newValues;
        $this->type      = $type;
        $this->sortOrder = $sortOrder;
    }
    
    
    /**
     * @param OptionId        $id
     * @param OptionDetails   $details
     * @param OptionValues    $values
     * @param NewOptionValues $newValues
     * @param OptionType      $type
     * @param int             $sortOrder
     *
     * @return Option
     */
    public static function create(
        OptionId $id,
        OptionDetails $details,
        OptionValues $values,
        NewOptionValues $newValues,
        OptionType $type,
        int $sortOrder
    ): Option {
    
        Assert::greaterThanEq($sortOrder, 0, 'Expected option sort order greater than or equal to %2$s. Got: %s');
        
        return new self($id, $details, $values, $newValues, $type, $sortOrder);
    }
    
    
    /**
     * Returns the ID of the option.
     *
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * Returns the label of the option for a given language code.
     *
     * @param string $languageCode
     *
     * @return string
     */
    public function label(string $languageCode): string
    {
        return $this->details->label($languageCode);
    }
    
    
    /**
     * Returns the admin label of the option for a given language code.
     *
     * @param string $languageCode
     *
     * @return string
     */
    public function adminLabel(string $languageCode): string
    {
        return $this->details->adminLabel($languageCode);
    }
    
    
    /**
     * Returns the description of the option for a given language code.
     *
     * @param string $languageCode
     *
     * @return string
     */
    public function description(string $languageCode): string
    {
        return $this->details->description($languageCode);
    }
    
    
    /**
     * Returns the already stored values of the option.
     *
     * @return OptionValues
     */
    public function values(): OptionValues
    {
        return $this->values;
    }
    
    
    /**
     * Returns the new (not stored) values of the option.
     *
     * @return NewOptionValues
     */
    public function newValues(): NewOptionValues
    {
        return $this->newValues;
    }
    
    
    /**
     * Returns the type of the option.
     *
     * @return string
     */
    public function type(): string
    {
        return $this->type->value();
    }
    
    
    /**
     * Returns the sort order of the option.
     *
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
    
    
    /**
     * Updates the details of this option.
     *
     * @param OptionDetails $newDetails
     */
    public function changeDetails(OptionDetails $newDetails): void
    {
        $this->details = $newDetails;
        
        $this->raiseEvent(OptionDetailsUpdated::create($this->id, $newDetails));
    }
    
    
    /**
     * Adds a new value to this option.
     *
     * @param NewOptionValue ...$newOptionValues
     */
    public function addNewValues(NewOptionValue ...$newOptionValues): void
    {
        $this->newValues = $this->newValues->with(...$newOptionValues);
        
        foreach ($newOptionValues as $newOptionValue) {
            $this->raiseEvent(NewOptionValueAdded::create($this->id, $newOptionValue));
        }
    }
    
    
    /**
     * Updates already stored values of this option.
     *
     * @param OptionValue ...$optionValues
     */
    public function changeValues(OptionValue ...$optionValues): void
    {
        $this->values = $this->values->with(...$optionValues);
        
        foreach ($optionValues as $optionValue) {
            $this->raiseEvent(OptionValueUpdated::create($this->id, $optionValue));
        }
    }
    
    
    /**
     * Removes already stored values from this option.
     *
     * @param OptionValueId ...$optionValueIds
     */
    public function removeValues(OptionValueId ...$optionValueIds): void
    {
        $this->values = $this->values->without(...$optionValueIds);
        
        foreach ($optionValueIds as $optionValueId) {
            $this->raiseEvent(OptionValueDeleted::create($this->id, $optionValueId));
        }
    }
    
    
    /**
     * Updates the type of this option.
     *
     * @param OptionType $newType
     */
    public function changeType(OptionType $newType): void
    {
        $this->type = $newType;
        
        $this->raiseEvent(OptionsTypeUpdated::create($this->id, $newType));
    }
    
    
    /**
     * Updates the sort order of this option.
     *
     * @param int $newSortOrder
     */
    public function changeSortOrder(int $newSortOrder): void
    {
        Assert::greaterThanEq($newSortOrder, 0, 'Expected option sort order greater than or equal to %2$s. Got: %s');
        
        $this->sortOrder = $newSortOrder;
        
        $this->raiseEvent(OptionsSortOrderUpdated::create($this->id, $newSortOrder));
    }
    
    
    /**
     * Returns the the data of this option as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'        => $this->id(),
            'type'      => $this->type(),
            'sortOrder' => $this->sortOrder(),
            'details'   => $this->details->toArray(),
            'values'    => $this->values->toArray(),
            'newValues' => $this->newValues->toArray(),
        ];
    }
}