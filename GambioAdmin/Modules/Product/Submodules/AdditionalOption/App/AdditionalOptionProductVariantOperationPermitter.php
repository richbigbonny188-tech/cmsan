<?php
/*--------------------------------------------------------------
   AdditionalOptionProductVariantOperationPermitter.php 2023-06-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFactory;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionRepository as AdditionalOptionRepositoryInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantOperationPermitter as ProductVariantOperationPermitterInterface;

/**
 * Class AdditionalOptionProductVariantOperationPermitter
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App
 */
class AdditionalOptionProductVariantOperationPermitter implements ProductVariantOperationPermitterInterface
{
    private ?array $optionsUsedAsAdditionalOption;
    
    
    /**
     * @param AdditionalOptionRepositoryInterface $repository
     * @param AdditionalOptionFactory             $factory
     */
    public function __construct(
        private AdditionalOptionRepositoryInterface $repository,
        private AdditionalOptionFactory             $factory
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsCreations(array ...$creationArgs): bool
    {
        foreach ($creationArgs as $args) {
            /** @var ProductId $productId */
            /** @var OptionAndOptionValueIds $combination */
            [$productId, $combination] = $args;
            
            $optionIds = array_column($combination->toArray(), 'optionId');
            $optionIds = array_unique($optionIds);
            
            if ($this->optionIdUsedAsAdditionalOption($productId->value(), ...$optionIds)) {
                return false;
            }
        }
        
        return true;
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsStorages(ProductVariant ...$variants): bool
    {
        return true;
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsDeletions(ProductVariantId ...$ids): bool
    {
        return true;
    }
    
    
    /**
     * @param int $productId
     * @param int ...$optionIds
     *
     * @return bool
     */
    protected function optionIdUsedAsAdditionalOption(
        int $productId,
        int ...$optionIds
    ): bool {
        if (isset($this->optionsUsedAsAdditionalOption) === false) {
            $productOptions = $this->repository->getAdditionalOptionsByProductId($this->factory->createProductId($productId));
            
            $this->optionsUsedAsAdditionalOption = array_column($productOptions->toArray(), 'optionId');
            $this->optionsUsedAsAdditionalOption = array_unique($this->optionsUsedAsAdditionalOption);
        }
        
        foreach ($optionIds as $optionId) {
            if (in_array($optionId, $this->optionsUsedAsAdditionalOption, true)) {
                return true;
            }
        }
        
        return false;
    }
}