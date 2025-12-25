<?php
/*--------------------------------------------------------------
   DeprecatedEventRaisingProductDownloadRepository.php 2023-06-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductDownloadStock;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\ProductDownload\Model\Events\ProductDownloadCreated;
use Gambio\Admin\Modules\ProductDownload\Model\Events\ProductDownloadDeleted;

/**
 * Class DeprecatedEventRaisingProductDownloadRepository
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App
 * @deprecated
 */
class DeprecatedEventRaisingProductDownloadRepository extends ProductDownloadRepository
{
    /**
     * @inheritDoc
     */
    public function createProductDownload(
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        ProductDownloadStock     $productDownloadStock,
        int                      $sortOrder = 0
    ): AdditionalOptionId {
        $id = parent::createProductDownload($productId,
                                            $optionAndOptionValueId,
                                            $imageListId,
                                            $optionValueCustomization,
                                            $productDownloadStock,
                                            $sortOrder);
        $this->dispatchEvent(ProductDownloadCreated::create($id));
        
        return $id;
    }
    
    /**
     * @inheritDoc
     */
    public function createMultipleProductDownloads(array ...$creationArguments): AdditionalOptionIds
    {
        $ids = parent::createMultipleProductDownloads(...$creationArguments);
        
        foreach ($ids as $id) {
            $this->dispatchEvent(ProductDownloadCreated::create($id));
        }
        
        return $ids;
    }
    
    /**
     * @inheritDoc
     */
    public function deleteProductDownloads(AdditionalOptionId ...$ids): void
    {
        parent::deleteProductDownloads(...$ids);
        
        foreach ($ids as $id) {
            $this->dispatchEvent(ProductDownloadDeleted::create($id));
        }
    }
    
    public function deleteAllProductDownloadsByProductId(ProductId $productId): void
    {
        $additionalOptionIds = $this->reader->getProductOptionIdsByProductId($productId->value());
        parent::deleteAllProductDownloadsByProductId($productId);;
        $additionalOptionIds = array_map([AdditionalOptionId::class, 'create'], $additionalOptionIds);
        foreach ($additionalOptionIds as $additionalOptionId) {
            $this->dispatchEvent(ProductDownloadDeleted::create($additionalOptionId));
        }
    }
}