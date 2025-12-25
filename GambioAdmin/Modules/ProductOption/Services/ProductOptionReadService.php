<?php
/*--------------------------------------------------------------------
 ProductOptionReadService.php 2021-04-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Services;

use Gambio\Admin\Modules\ProductOption\Model\Collections\ProductOptions;
use Gambio\Admin\Modules\ProductOption\Model\Exceptions\ProductOptionDoesNotExistException;
use Gambio\Admin\Modules\ProductOption\Model\ProductOption;

/**
 * Interface ProductOptionReadService
 * @package Gambio\Admin\Modules\ProductOption\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *             \Gambio\Admin\Modules\Product\Services\Proxies\AdditionalOptionReadServiceProxy
 */
interface ProductOptionReadService
{
    /**
     * @param int $productId
     *
     * @return ProductOptions
     */
    public function getProductOptionsByProductId(int $productId): ProductOptions;


    /**
     * @param int $productOptionId
     *
     * @return ProductOption
     *
     * @throws ProductOptionDoesNotExistException
     */
    public function getProductOptionById(int $productOptionId): ProductOption;
}