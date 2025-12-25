<?php
/* --------------------------------------------------------------
   OptionMapper.php 2020-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Data;

use Gambio\Admin\Modules\Option\Model\Collections\NewOptionValues;
use Gambio\Admin\Modules\Option\Model\Collections\OptionDetails;
use Gambio\Admin\Modules\Option\Model\Collections\OptionIds;
use Gambio\Admin\Modules\Option\Model\Collections\Options;
use Gambio\Admin\Modules\Option\Model\Collections\OptionValueDetails;
use Gambio\Admin\Modules\Option\Model\Collections\OptionValues;
use Gambio\Admin\Modules\Option\Model\Entities\OptionValue;
use Gambio\Admin\Modules\Option\Model\Option;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionDetail;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueDetail;
use Gambio\Admin\Modules\Option\Services\OptionFactory;

/**
 * Class OptionMapper
 *
 * @package Gambio\Admin\Modules\Option\App\Data
 */
class OptionMapper
{
    /**
     * @var OptionFactory
     */
    private $factory;
    
    
    /**
     * OptionMapper constructor.
     *
     * @param OptionFactory $factory
     */
    public function __construct(OptionFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param int $value
     *
     * @return OptionId
     */
    public function mapOptionId(int $value): OptionId
    {
        return $this->factory->createOptionId($value);
    }
    
    
    /**
     * @param int ...$values
     *
     * @return OptionIds
     */
    public function mapOptionIds(int ...$values): OptionIds
    {
        $ids = array_map([$this, 'mapOptionId'], $values);
        
        return $this->factory->createOptionIds(...$ids);
    }
    
    
    /**
     * @param array $optionsData
     *
     * @return Options
     */
    public function mapOptions(array $optionsData): Options
    {
        $options = array_map([$this, 'mapOption'], $optionsData);
        
        return Options::create(...$options);
    }
    
    
    /**
     * @param array $optionData
     *
     * @return Option
     */
    public function mapOption(array $optionData): Option
    {
        $id              = $this->factory->createOptionId($optionData['id']);
        $details         = $this->mapOptionDetails($optionData['details']);
        $optionValues    = $this->mapOptionValues($optionData['values']);
        $newOptionValues = $this->mapNewOptionValues($optionData['newValues']);
        $type            = $this->factory->createOptionType($optionData['type']);
        $sortOrder       = $optionData['sortOrder'];
        
        return Option::create($id, $details, $optionValues, $newOptionValues, $type, $sortOrder);
    }
    
    
    /**
     * @param array $detailsData
     *
     * @return OptionDetails
     */
    private function mapOptionDetails(array $detailsData): OptionDetails
    {
        $details = array_map(function (array $detailData): OptionDetail {
            return $this->factory->createOptionDetail($detailData['languageCode'],
                                                      $detailData['label'],
                                                      $detailData['adminLabel'],
                                                      $detailData['description']);
        },
            array_values($detailsData));
        
        return $this->factory->createOptionDetails(...$details);
    }
    
    
    /**
     * @param array $optionValuesData
     *
     * @return OptionValues
     */
    private function mapOptionValues(array $optionValuesData): OptionValues
    {
        $optionValues = array_map(function (array $optionValueData): OptionValue {
            $id             = $this->factory->createOptionValueId($optionValueData['id']);
            $details        = $this->mapOptionValueDetails($optionValueData['details']);
            $productDetails = $this->factory->createOptionValuesProductDetails($optionValueData['productDetails']['modelNumber'],
                                                                               $optionValueData['productDetails']['weight'],
                                                                               $optionValueData['productDetails']['price']);
            $stock          = $this->factory->createOptionValueStock($optionValueData['stock']['stockType'],
                                                                     $optionValueData['stock']['stock'],
                                                                     $optionValueData['stock']['stockCentrallyManaged']);
            $sortOrder      = $optionValueData['sortOrder'];
            $image          = $optionValueData['image'];
            
            return $this->factory->createOptionValue($id, $details, $productDetails, $stock, $sortOrder, $image);
        },
            $optionValuesData);
        
        return $this->factory->createOptionValues(...$optionValues);
    }
    
    
    /**
     * @param array $newOptionValuesData
     *
     * @return NewOptionValues
     */
    private function mapNewOptionValues(array $newOptionValuesData): NewOptionValues
    {
        return $this->factory->createNewOptionValues();
    }
    
    
    /**
     * @param array $optionValueDetailsData
     *
     * @return OptionValueDetails
     */
    private function mapOptionValueDetails(array $optionValueDetailsData): OptionValueDetails
    {
        $optionValues = array_map(function (array $optionValueDetailData): OptionValueDetail {
            return $this->factory->createOptionValueDetail($optionValueDetailData['languageCode'],
                                                           $optionValueDetailData['label'],
                                                           $optionValueDetailData['description']);
        },
            array_values($optionValueDetailsData));
        
        return $this->factory->createOptionValueDetails(...$optionValues);
    }
}