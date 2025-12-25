<?php
/*--------------------------------------------------------------------
 ProductOptionReadService.php 2023-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\App;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionReadService;
use Gambio\Admin\Modules\ProductOption\Model\Collections\ProductOptions;
use Gambio\Admin\Modules\ProductOption\Model\ProductOption;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionReadService as ProductOptionReadServiceInterface;

/**
 * Class ProductOptionReadService
 *
 * @package    Gambio\Admin\Modules\ProductOption\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionReadService
 */
class ProductOptionReadService implements ProductOptionReadServiceInterface
{
    public function __construct(private AdditionalOptionReadService $service) { }
    
    
    /**
     * @inheritDoc
     */
    public function getProductOptionsByProductId(int $productId): ProductOptions
    {
        return $this->service->getAdditionalOptionsByProductId($productId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductOptionById(int $productOptionId): ProductOption
    {
        return $this->service->getAdditionalOptionById($productOptionId);
    }
}