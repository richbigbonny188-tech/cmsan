<?php
/*--------------------------------------------------------------
   DeprecatedEventRaisingAdditionalOptionMapper.php 2023-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\DeprecatedEventRaisingAdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ProductId;

/**
 * Class DeprecatedEventRaisingAdditionalOptionMapper
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data
 * @deprecated will be removed with GX 4.11. This class is used to bridge the old ProductOptions
 *             and the refactored AdditionalOptions for the duration of GX 4.9
 */
class DeprecatedEventRaisingAdditionalOptionMapper extends AdditionalOptionMapper
{
    protected function createAdditionalOption(
        AdditionalOptionId       $id,
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        AdditionalOptionStock    $additionalOptionStock,
        int                      $sortOrder
    ): AdditionalOption {
        return DeprecatedEventRaisingAdditionalOption::create($id,
                                                              $productId,
                                                              $optionAndOptionValueId,
                                                              $imageListId,
                                                              $optionValueCustomization,
                                                              $additionalOptionStock,
                                                              $sortOrder);
    }
}