<?php
/*--------------------------------------------------------------------
 AdditionalOptionWriteService.php 2023-06-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFactory;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionRepository as AdditionalOptionRepositoryInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionWriteService as AdditionalOptionWriteServiceInterface;

/**
 * Class AdditionalOptionWriteService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App
 */
class AdditionalOptionWriteService implements AdditionalOptionWriteServiceInterface
{
    
    /**
     * AdditionalOptionWriteService constructor.
     *
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
    public function createAdditionalOption(
        int                      $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        AdditionalOptionStock    $additionalOptionStock,
        int                      $sortOrder = 0
    ): AdditionalOptionId {
        return $this->repository->createAdditionalOption($this->factory->createProductId($productId),
                                                         $optionAndOptionValueId,
                                                         $imageListId,
                                                         $optionValueCustomization,
                                                         $additionalOptionStock,
                                                         $sortOrder);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleAdditionalOptions(array ...$creationArguments): AdditionalOptionIds
    {
        return $this->repository->createMultipleAdditionalOptions(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeAdditionalOptions(AdditionalOption ...$additionalOptions): void
    {
        $this->repository->storeAdditionalOptions(...$additionalOptions);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAdditionalOptions(int ...$ids): void
    {
        $this->repository->deleteAdditionalOptions(...array_map([$this->factory, 'createAdditionalOptionId'], $ids));
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllAdditionalOptionsByProductId(int $productId): void
    {
        $productId = $this->factory->createProductId($productId);
        
        $this->repository->deleteAllAdditionalOptionsByProductId($productId);
    }
}