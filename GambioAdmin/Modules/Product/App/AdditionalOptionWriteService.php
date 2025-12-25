<?php
/*--------------------------------------------------------------
   AdditionalOptionWriteServiceProxy.php 2023-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\App;

use Gambio\Admin\Modules\Product\Services\AdditionalOptionWriteService as AdditionalOptionWriteServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services as Submodule;

/**
 * Class AdditionalOptionWriteServiceProxy
 *
 * @package Gambio\Admin\Modules\Product\App\Proxies
 */
class AdditionalOptionWriteService implements AdditionalOptionWriteServiceInterface
{
    public function __construct(private Submodule\AdditionalOptionWriteService $submoduleWriteService) { }
    
    
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
        return $this->submoduleWriteService->createAdditionalOption($productId,
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
        return $this->submoduleWriteService->createMultipleAdditionalOptions(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeAdditionalOptions(AdditionalOption ...$additionalOptions): void
    {
        $this->submoduleWriteService->storeAdditionalOptions(...$additionalOptions);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAdditionalOptions(int ...$ids): void
    {
        $this->submoduleWriteService->deleteAdditionalOptions(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllAdditionalOptionsByProductId(int $productId): void
    {
        $this->submoduleWriteService->deleteAllAdditionalOptionsByProductId($productId);
    }
}