<?php
/*------------------------------------------------------------------------------
 OptionFactory.php 2020-04-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Services;

use Gambio\Admin\Modules\Option\Model\Collections\NewOptionValues;
use Gambio\Admin\Modules\Option\Model\Collections\OptionDetails;
use Gambio\Admin\Modules\Option\Model\Collections\OptionIds;
use Gambio\Admin\Modules\Option\Model\Collections\OptionValueDetails;
use Gambio\Admin\Modules\Option\Model\Collections\OptionValues;
use Gambio\Admin\Modules\Option\Model\Entities\OptionValue;
use Gambio\Admin\Modules\Option\Model\ValueObjects\NewOptionValue;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionDetail;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionType;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueDetail;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueId;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValuesProductDetails;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueStock;

/**
 * Class OptionFactory
 *
 * @package Gambio\Admin\Modules\Option\Services
 */
class OptionFactory
{
    /**
     * Creates and returns an option detail.
     *
     * @param string $languageCode
     * @param string $label
     * @param string $adminLabel
     * @param string $description
     *
     * @return OptionDetail
     */
    public function createOptionDetail(
        string $languageCode,
        string $label,
        string $adminLabel,
        string $description
    ): OptionDetail {
        return OptionDetail::create($languageCode, $label, $adminLabel, $description);
    }
    
    
    /**
     * Creates and returns a collection option details.
     *
     * @param OptionDetail ...$optionDetails
     *
     * @return OptionDetails
     */
    public function createOptionDetails(OptionDetail...$optionDetails): OptionDetails
    {
        return OptionDetails::create(...$optionDetails);
    }
    
    
    /**
     * Creates and returns an option value.
     *
     * @param OptionValueId              $id
     * @param OptionValueDetails         $details
     * @param OptionValuesProductDetails $productDetails
     * @param OptionValueStock           $stock
     * @param int                        $sortOrder
     * @param string                     $image
     *
     * @return OptionValue
     */
    public function createOptionValue(
        OptionValueId $id,
        OptionValueDetails $details,
        OptionValuesProductDetails $productDetails,
        OptionValueStock $stock,
        int $sortOrder,
        string $image
    ): OptionValue {
        return OptionValue::create($id, $details, $productDetails, $stock, $sortOrder, $image);
    }
    
    
    /**
     * Creates and returns a collection of option values.
     *
     * @param OptionValue ...$optionValues
     *
     * @return OptionValues
     */
    public function createOptionValues(OptionValue ...$optionValues): OptionValues
    {
        return OptionValues::create(...$optionValues);
    }
    
    
    /**
     * Creates and returns a new option value.
     *
     * @param OptionValueDetails         $details
     * @param OptionValuesProductDetails $productDetails
     * @param OptionValueStock           $stock
     * @param int                        $sortOrder
     * @param string                     $image
     *
     * @return NewOptionValue
     */
    public function createNewOptionValue(
        OptionValueDetails $details,
        OptionValuesProductDetails $productDetails,
        OptionValueStock $stock,
        int $sortOrder,
        string $image
    ): NewOptionValue {
        return NewOptionValue::create($details, $productDetails, $stock, $sortOrder, $image);
    }
    
    
    /**
     * Creates and returns a collection of new option values.
     *
     * @param NewOptionValue ...$newOptionValues
     *
     * @return NewOptionValues
     */
    public function createNewOptionValues(NewOptionValue ...$newOptionValues): NewOptionValues
    {
        return NewOptionValues::create(...$newOptionValues);
    }
    
    
    /**
     * Creates and returns an option value ID.
     *
     * @param int $id
     *
     * @return OptionValueId
     */
    public function createOptionValueId(int $id): OptionValueId
    {
        return OptionValueId::create($id);
    }
    
    
    /**
     * Creates and returns option value product details.
     *
     * @param string $modelNumber
     * @param float  $weight
     * @param float  $price
     *
     * @return OptionValuesProductDetails
     */
    public function createOptionValuesProductDetails(
        string $modelNumber,
        float $weight,
        float $price
    ): OptionValuesProductDetails {
        return OptionValuesProductDetails::create($modelNumber, $weight, $price);
    }
    
    
    /**
     * Creates and returns an option value stock.
     *
     * @param string $stockType
     * @param float  $stock
     * @param bool   $stockCentrallyManaged
     *
     * @return OptionValueStock
     */
    public function createOptionValueStock(
        string $stockType,
        float $stock,
        bool $stockCentrallyManaged
    ): OptionValueStock {
        return OptionValueStock::create($stockType, $stock, $stockCentrallyManaged);
    }
    
    
    /**
     * Creates and returns an option value detail.
     *
     * @param string $languageCode
     * @param string $label
     * @param string $description
     *
     * @return OptionValueDetail
     */
    public function createOptionValueDetail(string $languageCode, string $label, string $description): OptionValueDetail
    {
        return OptionValueDetail::create($languageCode, $label, $description);
    }
    
    
    /**
     * Creates and returns a collection of option value details.
     *
     * @param OptionValueDetail ...$optionValueDetails
     *
     * @return OptionValueDetails
     */
    public function createOptionValueDetails(OptionValueDetail ...$optionValueDetails): OptionValueDetails
    {
        return OptionValueDetails::create(...$optionValueDetails);
    }
    
    
    /**
     * Creates and returns an option ID.
     *
     * @param int $id
     *
     * @return OptionId
     */
    public function createOptionId(int $id): OptionId
    {
        return OptionId::create($id);
    }
    
    
    /**
     * Creates and returns a collection of option IDs.
     *
     * @param OptionId ...$ids
     *
     * @return OptionIds
     */
    public function createOptionIds(OptionId ...$ids): OptionIds
    {
        return OptionIds::create(...$ids);
    }
    
    
    /**
     * Creates and returns an option type.
     *
     * @param string $value
     *
     * @return OptionType
     */
    public function createOptionType(string $value): OptionType
    {
        return OptionType::create($value);
    }
}