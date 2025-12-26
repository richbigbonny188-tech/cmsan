<?php
/*--------------------------------------------------------------
   ProductVariantOperationPermitter.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Services;


use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;

/**
 * Interface ProductVariantOperationPermitter
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Services
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