<?php
/*--------------------------------------------------------------
   ProductVariantProductOptionOperationPermitter.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App;

use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsReader;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionOperationPermitter;

/**
 * Class ProductVariantAdditionalOptionOperationPermitter
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App
 */
class ProductVariantProductOptionOperationPermitter implements AdditionalOptionOperationPermitter
{
    /**
     * @param ProductVariantsReader $reader
     */
    public function __construct(private ProductVariantsReader $reader) { }
    
    
    /**
     * @inheritDoc
     */
    public function permitsCreations(array ...$creationArgs): bool
    {
        foreach ($creationArgs as $args) {
            /** @var ProductId $productId */
            $productId = $args[0];
            /** @var OptionAndOptionValueId $optionAndOptionValueId */
            $optionAndOptionValueId = $args[1];
            
            if (in_array($optionAndOptionValueId->optionId(), $this->optionsUsedAsVariants($productId), true)) {
                return false;
            }
        }
        
        return true;
    }
    
    
    /**
     * @param ProductId $productId
     *
     * @return array
     */
    private function optionsUsedAsVariants(ProductId $productId): array
    {
        $result   = [];
        $variants = $this->reader->getProductVariantsByProductId(\Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId::create($productId->value()));
        
        foreach ($variants as $variant) {
            foreach (explode('|', $variant['combination']) as $optionOptionValue) {
                $result[] = (int)(explode('-', $optionOptionValue)[0]);
            }
        }
        
        return array_unique($result);
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsStorages(AdditionalOption ...$productOption): bool
    {
        return true;
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsDeletions(AdditionalOptionId ...$ids): bool
    {
        return true;
    }
}