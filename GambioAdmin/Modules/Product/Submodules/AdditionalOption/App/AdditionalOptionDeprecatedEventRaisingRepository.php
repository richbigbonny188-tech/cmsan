<?php
/*--------------------------------------------------------------
   AdditionalOptionDeprecatedEventRaisingRepository.php 2023-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptions;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\AdditionalOptionAlreadyExistsException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\InsertionOfAdditionalOptionsFailedException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\ProductOption\Model\Events\ProductOptionCreated;
use Gambio\Admin\Modules\ProductOption\Model\Events\ProductOptionDeleted;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Class AdditionalOptionDeprecatedEventRaisingRepository
 *
 * @package    Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App
 * @deprecated will be removed with GX 4.11. This class is used to bridge the old ProductOptions
 *             and the refactored AdditionalOptions for the duration of GX 4.9
 */
class AdditionalOptionDeprecatedEventRaisingRepository extends AdditionalOptionRepository
{
    /**
     * @param ProductId                $productId
     * @param OptionAndOptionValueId   $optionAndOptionValueId
     * @param ImageListId              $imageListId
     * @param OptionValueCustomization $optionValueCustomization
     * @param AdditionalOptionStock    $additionalOptionStock
     * @param int                      $sortOrder
     *
     * @return AdditionalOptionId
     * @throws AdditionalOptionAlreadyExistsException
     * @throws InsertionOfAdditionalOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function createAdditionalOption(
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        AdditionalOptionStock    $additionalOptionStock,
        int                      $sortOrder = 0
    ): AdditionalOptionId {
        $id = parent::createAdditionalOption($productId,
                                             $optionAndOptionValueId,
                                             $imageListId,
                                             $optionValueCustomization,
                                             $additionalOptionStock,
                                             $sortOrder);
        
        $this->dispatchEvent(ProductOptionCreated::create($id));
        
        return $id;
    }
    
    
    /**
     * @param array ...$creationArguments
     *
     * @return AdditionalOptionIds
     * @throws AdditionalOptionAlreadyExistsException
     * @throws InsertionOfAdditionalOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function createMultipleAdditionalOptions(array ...$creationArguments): AdditionalOptionIds
    {
        $ids = parent::createMultipleAdditionalOptions(...$creationArguments);
        
        foreach ($ids as $id) {
            $this->dispatchEvent(ProductOptionCreated::create($id));
        }
        
        return $ids;
    }
    
    
    public function storeAdditionalOptions(AdditionalOption ...$additionalOptions): void
    {
        parent::storeAdditionalOptions(...$additionalOptions);
    }
    
    
    public function deleteAdditionalOptions(AdditionalOptionId ...$ids): void
    {
        parent::deleteAdditionalOptions(...$ids);
        
        foreach ($ids as $id) {
            $this->dispatchEvent(ProductOptionDeleted::create($id));
        }
    }
    
    
    public function deleteAllAdditionalOptionsByProductId(ProductId $productId): void
    {
        $additionalOptionIds = $this->reader->getAdditionalOptionIdsByProductId($productId->value());
        parent::deleteAllAdditionalOptionsByProductId($productId);
        
        $additionalOptionIds = array_map([AdditionalOptionId::class, 'create'], $additionalOptionIds);
        foreach ($additionalOptionIds as $additionalOptionId) {
            $this->dispatchEvent(ProductOptionDeleted::create($additionalOptionId));
        }
    }
}