<?php
/*--------------------------------------------------------------
   DeprecatedEventRaisingProductDownloadMapper.php 2023-06-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App\Data;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\DeprecatedEventRaisingProductDownload;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductDownloadStock;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductId;

/**
 * Class DeprecatedEventRaisingProductDownloadMapper
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App\Data
 * @deprecated
 */
class DeprecatedEventRaisingProductDownloadMapper extends ProductDownloadMapper
{
    /**
     * @param AdditionalOptionId       $productOptionId
     * @param ProductId                $productId
     * @param OptionAndOptionValueId   $optionAndOptionValueId
     * @param ImageListId              $imageListId
     * @param OptionValueCustomization $optionValueCustomization
     * @param ProductDownloadStock     $productOptionStock
     * @param int                      $sortOrder
     *
     * @return ProductDownload
     */
    protected function createProductDownload(
        additionalOptionId       $productOptionId,
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        ProductDownloadStock     $productOptionStock,
        int                      $sortOrder
    ): ProductDownload {
        return DeprecatedEventRaisingProductDownload::create($productOptionId,
                                       $productId,
                                       $optionAndOptionValueId,
                                       $imageListId,
                                       $optionValueCustomization,
                                       $productOptionStock,
                                       $sortOrder);
    }
}