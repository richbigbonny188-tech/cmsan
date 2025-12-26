<?php
/*--------------------------------------------------------------
   ProductVariantOperationPermitter.php 2021-10-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductVariant\Services;


use Gambio\Admin\Modules\ProductVariant\Model\ProductVariant;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductVariantId;

/**
 * Interface ProductVariantOperationPermitter
 * @package Gambio\Admin\Modules\ProductVariant\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This interface will be deleted with 4.11.
 */
interface ProductVariantOperationPermitter
{
    /**
     * Checks the permission of the create operation.
     *
     * @param array ...$creationArgs
     *
     * @return bool
     */
    public function permitsCreations(array ...$creationArgs): bool;
    
    /**
     * Checks the permission of the store operation.
     *
     * @param ProductVariant ...$variants
     *
     * @return bool
     */
    public function permitsStorages(ProductVariant ...$variants): bool;
    
    
    /**
     * Checks the permission of the delete operation.
     *
     * @param ProductVariantId ...$ids
     *
     * @return bool
     */
    public function permitsDeletions(ProductVariantId ...$ids): bool;
}