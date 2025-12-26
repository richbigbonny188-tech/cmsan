<?php
/* --------------------------------------------------------------
   ProductVariantCombinationGenerator.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;
use Webmozart\Assert\Assert;

/**
 * Class ProductVariantCombinationGenerator
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App
 */
class ProductVariantCombinationGenerator
{
    /**
     * ProductVariantCombinationGenerator constructor.
     *
     * @param ProductVariantFactory $factory
     */
    public function __construct(private ProductVariantFactory $factory) { }
    
    
    /**
     * @param array $optionAndOptionValueIds
     * @param int   $limit
     * @param int   $offset
     *
     * @return OptionAndOptionValueIds[]
     */
    public function getCombinations(
        array $optionAndOptionValueIds,
        int   $limit = 100,
        int   $offset = 0
    ): array {
        Assert::allKeyExists($optionAndOptionValueIds,
                             'optionId',
                             'Given option and option value pairs array must have a "optionId" key.');
        Assert::allKeyExists($optionAndOptionValueIds,
                             'optionValueId',
                             'Given option and option value pairs array must have a "optionValueId" key.');
        
        $combinations                      = [];
        $groupedOptionAndOptionValueIds    = $this->groupOptionAndOptionIdsByOptions($optionAndOptionValueIds);
        $determinedOptionValueCombinations = $this->determinedOptionValueCombinations($groupedOptionAndOptionValueIds);
        $slicedOptionValueCombinations     = array_slice($determinedOptionValueCombinations, $offset, $limit);
        
        foreach ($slicedOptionValueCombinations as $optionValueCombination) {
            $optionAndOptionValueIds = array_map(function (array $optionAndOptionValue): OptionAndOptionValueId {
                return $this->factory->createOptionAndOptionValueId($optionAndOptionValue['optionId'],
                                                                    $optionAndOptionValue['optionValueId']);
            },
                $optionValueCombination);
            
            $combinations[] = $this->factory->createOptionAndOptionValueIds(...$optionAndOptionValueIds);
        }
        
        return $combinations;
    }
    
    
    /**
     * @param array $optionAndOptionValueIds
     *
     * @return array<string, int>
     */
    private function groupOptionAndOptionIdsByOptions(array $optionAndOptionValueIds): array
    {
        /** @var array<int, array<string, int>> $optionValuesToOptionMap */
        $optionValuesToOptionMap = [];
        
        foreach ($optionAndOptionValueIds as $optionAndOptionValueId) {
            $optionValuesToOptionMap[$optionAndOptionValueId['optionId']][] = $optionAndOptionValueId;
        }
        
        return array_values($optionValuesToOptionMap);
    }
    
    
    /**
     * @param array $groupedOptionAndOptionValueIds
     * @param int   $iteration
     *
     * @return array<int, array<string, int>>
     */
    private function determinedOptionValueCombinations(array $groupedOptionAndOptionValueIds, $iteration = 0): array
    {
        if ($iteration >= count($groupedOptionAndOptionValueIds)) {
            return [];
        }
        
        // get combinations from subsequent groupedOptionAndOptionValueIds
        $nextOptionValueCombinations = $this->determinedOptionValueCombinations($groupedOptionAndOptionValueIds,
                                                                                $iteration + 1);
        
        $result = [];
        
        // concat each array from nextOptionValueCombinations with each element from $groupedOptionAndOptionValueIds[$iteration]
        foreach ($groupedOptionAndOptionValueIds[$iteration] as $currentOptionValue) {
            if (count($nextOptionValueCombinations) === 0) {
                $result[] = [$currentOptionValue];
                continue;
            }
            
            foreach ($nextOptionValueCombinations as $otherCombination) {
                // then add current option value to this determined combination
                $result[] = array_merge([$currentOptionValue], $otherCombination);
            }
        }
        
        return $result;
    }
}